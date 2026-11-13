<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Services\IntegrationSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regression test for the production authentication outage.
 *
 * The published app logs in successfully but every protected screen
 * ("طلباتي", "حسابي", العناوين) answered "Unauthenticated". Root cause:
 * Passport 13 defaults Passport::$clientUuids = true, so ClientRepository::find()
 * demands a UUID client id, while this project's oauth_clients.id is a legacy
 * auto-increment integer. find() then returned null and TokenGuard rejected
 * every valid access token with a silent 401.
 *
 * These tests use an INTEGER client id (exactly like production) so the bug
 * cannot silently return.
 */
class PassportTokenAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Passport tables are not part of database/migrations in this project.
        // The schema mirrors production exactly (legacy Passport 10/11 layout
        // with an INTEGER client id) so the UUID/integer mismatch — the real
        // production outage — is actually exercised.
        \Illuminate\Support\Facades\Schema::create('oauth_clients', function ($t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name');
            $t->string('secret', 100)->nullable();
            $t->string('provider')->nullable();
            $t->text('redirect');
            $t->boolean('personal_access_client')->default(0);
            $t->boolean('password_client')->default(0);
            $t->boolean('revoked')->default(0);
            $t->timestamps();
        });

        DB::table('oauth_clients')->insert([
            'id' => 1,
            'user_id' => null,
            'name' => 'Regression Personal Access Client',
            // A non-empty secret makes the client "confidential", which is what
            // grants it the personal_access grant type (production has one).
            'secret' => Hash::make('regression-client-secret'),
            'provider' => null,
            'redirect' => '[]',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\Schema::create('oauth_access_tokens', function ($t) {
            $t->string('id', 100)->primary();
            $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->unsignedBigInteger('client_id');
            $t->string('name')->nullable();
            $t->text('scopes')->nullable();
            $t->boolean('revoked')->default(0);
            $t->timestamps();
            $t->timestamp('expires_at')->nullable();
        });

        \Illuminate\Support\Facades\Schema::create('oauth_refresh_tokens', function ($t) {
            $t->string('id', 100)->primary();
            $t->string('access_token_id', 100)->index();
            $t->boolean('revoked')->default(0);
            $t->timestamp('expires_at')->nullable();
        });

        config(['auth.guards.api' => ['driver' => 'passport', 'provider' => 'users']]);

        // Passport caches the resolved personal access client id in a static
        // property that survives across tests in the same process. Reset it so
        // tokens are always issued against this test's client.
        $ref = new \ReflectionClass(\Laravel\Passport\Passport::class);
        foreach (['personalAccessClientId', 'personalAccessClient'] as $name) {
            if ($ref->hasProperty($name)) {
                $p = $ref->getProperty($name);
                $p->setAccessible(true);
                $p->setValue(null, null);
            }
        }

        // Passport rejects a token when oauth_user_id equals oauth_client_id
        // (it then assumes a client-credentials token). Production users have
        // ids far above the client id, so reserve id 1 for the client here too.
        User::create([
            'first_name' => 'Reserved',
            'last_name' => 'Id',
            'mobile' => '966500000999',
            'email' => 'reserved-id@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        Role::findOrCreate('customer', 'web');
    }

    private function activeCustomer(string $email = 'passport-reg@example.test', string $mobile = '966500000901'): User
    {
        $user = User::create([
            'first_name' => 'Passport',
            'last_name' => 'Regression',
            'mobile' => $mobile,
            'email' => $email,
            'password' => Hash::make('password123'),
            'is_active' => true,
            'phone_verification_required' => false,
        ]);
        $user->assignRole('customer');
        (new CustomerRepository())->storeByUser($user);

        return $user;
    }

    public function test_personal_access_client_with_integer_id_is_resolvable(): void
    {
        $repo = app(\Laravel\Passport\ClientRepository::class);

        $this->assertNotNull($repo->find(1), 'ClientRepository::find() must resolve integer client ids');
        $this->assertNotNull($repo->findActive(1), 'ClientRepository::findActive() must resolve integer client ids');
        $this->assertFalse(\Laravel\Passport\Passport::$clientUuids, 'Passport::$clientUuids must be disabled for legacy integer client ids');
    }

    public function test_login_token_is_accepted_by_protected_api_routes(): void
    {
        $this->activeCustomer();

        $login = $this->postJson('/api/login', [
            'contact' => 'passport-reg@example.test',
            'password' => 'password123',
        ]);

        $login->assertStatus(200);
        $token = $login->json('data.access.token');
        $this->assertNotEmpty($token, 'login must return data.access.token');

        // Real bearer authentication through the Passport guard (no actingAs).
        $headers = ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'];

        $this->getJson('/api/customer', $headers)->assertStatus(200);
        $this->getJson('/api/orders', $headers)->assertStatus(200);
        $this->getJson('/api/addresses', $headers)->assertStatus(200);
        $this->getJson('/api/customer/join-request-status', $headers)->assertStatus(200);
    }

    public function test_orders_and_addresses_answers_are_parseable_by_the_app(): void
    {
        $this->activeCustomer('orders-reg@example.test', '966500000902');

        $token = $this->postJson('/api/login', [
            'contact' => 'orders-reg@example.test',
            'password' => 'password123',
        ])->json('data.access.token');

        $headers = ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'];

        $customer = $this->getJson('/api/customer', $headers)->assertStatus(200)->json('data');
        $this->assertArrayHasKey('customer', $customer);

        $orders = $this->getJson('/api/orders', $headers)->assertStatus(200)->json('data');
        $this->assertArrayHasKey('orders', $orders);

        $addresses = $this->getJson('/api/addresses', $headers)->assertStatus(200)->json('data');
        $this->assertArrayHasKey('addresses', $addresses);
    }

    public function test_invalid_token_is_still_rejected(): void
    {
        $this->activeCustomer('invalid-reg@example.test', '966500000903');

        $this->getJson('/api/customer', [
            'Authorization' => 'Bearer ' . str_repeat('z', 80),
            'Accept' => 'application/json',
        ])->assertStatus(401);

        $this->getJson('/api/customer', ['Accept' => 'application/json'])->assertStatus(401);
    }

    public function test_login_response_keeps_the_legacy_fields_the_app_uses(): void
    {
        $this->activeCustomer('legacy-reg@example.test', '966500000904');

        $response = $this->postJson('/api/login', [
            'contact' => 'legacy-reg@example.test',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.access.auth_type', 'Bearer');
        $this->assertNotEmpty($response->json('data.access.token'));
        $this->assertNotEmpty($response->json('data.access.expires_at'));
        $response->assertJsonStructure([
            'message',
            'data' => ['user', 'access' => ['auth_type', 'token', 'expires_at']],
        ]);
    }
}

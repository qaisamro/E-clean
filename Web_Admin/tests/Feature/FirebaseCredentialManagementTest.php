<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Firebase\ServiceAccountTokenProvider;
use App\Services\IntegrationSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FirebaseCredentialManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();

        $dir = storage_path('app/private/firebase');
        if (is_dir($dir)) {
            foreach (glob($dir . '/*') as $item) {
                if (is_file($item)) {
                    @unlink($item);
                } elseif (is_dir($item)) {
                    foreach (glob($item . '/*') as $inner) {
                        @unlink($inner);
                    }
                    @rmdir($item);
                }
            }
        }
    }

    private function rootUser(): User
    {
        $user = User::create([
            'first_name' => 'Root',
            'last_name' => 'Test',
            'mobile' => 'root-firebase-mobile',
            'email' => 'root-firebase@example.test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('root', 'web'));

        return $user;
    }

    private function serviceAccountPayload(array $overrides = []): array
    {
        return array_merge([
            'project_id' => 'elite-clean-test',
            'client_email' => 'firebase@elite-clean-test.iam.gserviceaccount.com',
            'private_key' => "-----BEGIN PRIVATE KEY-----\ntest-private-key\n-----END PRIVATE KEY-----\n",
        ], $overrides);
    }

    private function fakeTokenProvider(): void
    {
        $this->app->instance(ServiceAccountTokenProvider::class, new class extends ServiceAccountTokenProvider {
            public function fetch(array $credentials): ?string
            {
                return 'oauth-test-token';
            }
        });
    }

    public function test_non_super_admin_cannot_upload_credentials(): void
    {
        $customer = User::create([
            'first_name' => 'Cust',
            'last_name' => 'Test',
            'mobile' => 'cust-firebase-mobile',
            'email' => 'cust-firebase@example.test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $customer->assignRole(Role::findOrCreate('customer', 'web'));

        $file = UploadedFile::fake()->createWithContent(
            'service.json',
            json_encode($this->serviceAccountPayload())
        );

        $this->actingAs($customer)
            ->post(route('integrations.firebase.credentials.upload'), ['service_account' => $file])
            ->assertForbidden();
    }

    public function test_valid_service_account_is_stored_and_not_leaked(): void
    {
        $this->fakeTokenProvider();
        Http::fake([
            'https://fcm.googleapis.com/v1/projects/elite-clean-test/messages:send' => Http::response([
                'error' => ['status' => 'NOT_FOUND', 'details' => [['errorCode' => 'UNREGISTERED']]],
            ], 404),
        ]);

        $root = $this->rootUser();
        $file = UploadedFile::fake()->createWithContent(
            'service.json',
            json_encode($this->serviceAccountPayload())
        );

        $response = $this->actingAs($root)
            ->post(route('integrations.firebase.credentials.upload'), [
                'service_account' => $file,
            ]);

        $response->assertRedirect();
        $this->assertFileExists(storage_path('app/private/firebase/credentials.json'));
        $contents = (string) file_get_contents(storage_path('app/private/firebase/credentials.json'));
        $this->assertStringContainsString('elite-clean-test', $contents);
        $this->assertStringNotContainsString('test-private-key', $response->getContent());

        $settings = app(IntegrationSettingsService::class);
        $this->assertSame('', (string) $settings->get('firebase.credentials_json', ''));
        $this->assertSame('elite-clean-test', (string) $settings->get('firebase.project_id', ''));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'firebase.credentials.uploaded',
            'group_name' => 'firebase',
        ]);
    }

    public function test_invalid_service_account_is_rejected_without_storing(): void
    {
        $root = $this->rootUser();
        $file = UploadedFile::fake()->createWithContent(
            'service.json',
            json_encode(['project_id' => 'elite-clean-test'])
        );

        $this->actingAs($root)
            ->post(route('integrations.firebase.credentials.upload'), ['service_account' => $file])
            ->assertRedirect();

        $this->assertFileDoesNotExist(storage_path('app/private/firebase/credentials.json'));
    }

    public function test_project_id_mismatch_is_rejected(): void
    {
        $settings = app(IntegrationSettingsService::class);
        $settings->set('firebase.project_id', 'expected-project', ['group' => 'firebase']);

        $root = $this->rootUser();
        $file = UploadedFile::fake()->createWithContent(
            'service.json',
            json_encode($this->serviceAccountPayload(['project_id' => 'other-project']))
        );

        $this->actingAs($root)
            ->post(route('integrations.firebase.credentials.upload'), ['service_account' => $file])
            ->assertRedirect();

        $this->assertFileDoesNotExist(storage_path('app/private/firebase/credentials.json'));
    }

    public function test_android_config_is_stored_with_safe_info(): void
    {
        $root = $this->rootUser();
        $payload = [
            'project_id' => 'elite-clean-test',
            'client' => [[
                'client_info' => [
                    'mobilesdk_app_id' => '1:123:android:abc',
                    'android_client_info' => ['package_name' => 'com.example.eliteclean'],
                ],
            ]],
        ];
        $file = UploadedFile::fake()->createWithContent('google-services.json', json_encode($payload));

        $this->actingAs($root)
            ->post(route('integrations.firebase.android-config.upload'), ['android_config' => $file])
            ->assertRedirect();

        $this->assertFileExists(storage_path('app/private/firebase/google-services.json'));
        $contents = (string) file_get_contents(storage_path('app/private/firebase/google-services.json'));
        $this->assertStringContainsString('com.example.eliteclean', $contents);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'firebase.android_config.uploaded',
            'group_name' => 'firebase',
        ]);
    }

    public function test_rollback_restores_backup(): void
    {
        $this->fakeTokenProvider();
        Http::fake([
            'https://fcm.googleapis.com/v1/projects/elite-clean-test/messages:send' => Http::response([
                'error' => ['status' => 'NOT_FOUND', 'details' => [['errorCode' => 'UNREGISTERED']]],
            ], 404),
        ]);

        $root = $this->rootUser();

        // First upload creates a backup only if a file already exists; seed one.
        $dir = storage_path('app/private/firebase');
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        file_put_contents($dir . '/credentials.json', json_encode($this->serviceAccountPayload(['project_id' => 'old-project', 'client_email' => 'old@example.com'])));

        $file = UploadedFile::fake()->createWithContent(
            'service.json',
            json_encode($this->serviceAccountPayload())
        );

        $this->actingAs($root)
            ->post(route('integrations.firebase.credentials.upload'), ['service_account' => $file])
            ->assertRedirect();

        $this->assertTrue(app(\App\Services\FirebaseCredentialManager::class)->hasServiceAccountBackup());

        $this->actingAs($root)
            ->post(route('integrations.firebase.credentials.rollback'))
            ->assertRedirect();

        $restored = json_decode((string) file_get_contents($dir . '/credentials.json'), true);
        $this->assertSame('old-project', $restored['project_id'] ?? '');
    }
}

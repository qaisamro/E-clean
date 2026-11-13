<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Services\ApiKeyService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ApiKeyController extends Controller
{
    public function __construct(
        private ApiKeyService $keys,
        private AuditLogger $audit
    ) {
    }

    public function index()
    {
        $this->authorizeSuperAdmin();

        $keys = ApiKey::with('creator')->latest()->paginate(30);
        $scopes = ApiKeyService::SCOPES;

        return view('integrations.api-keys', compact('keys', 'scopes'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'scopes' => ['required', 'array', 'min:1'],
            'scopes.*' => ['string', Rule::in(array_keys(ApiKeyService::SCOPES))],
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
        ]);

        $expiresAt = !empty($validated['expires_in_days'])
            ? Carbon::now()->addDays((int) $validated['expires_in_days'])
            : null;

        $generated = $this->keys->generate(
            $validated['name'],
            $validated['scopes'],
            $expiresAt,
            auth()->id()
        );

        return back()
            ->with('success', 'تم إنشاء مفتاح API. انسخه الآن لأنه لن يظهر مرة أخرى.')
            ->with('api_key_plain', $generated['plain']);
    }

    public function revoke(ApiKey $apiKey)
    {
        $this->authorizeSuperAdmin();

        $this->keys->revoke($apiKey);

        return back()->with('success', 'تم إلغاء مفتاح API.');
    }

    public function destroy(ApiKey $apiKey)
    {
        $this->authorizeSuperAdmin();

        $this->audit->log('api_key.deleted', 'api', 'api_key:' . $apiKey->id, ['name' => $apiKey->name]);
        $apiKey->delete();

        return back()->with('success', 'تم حذف مفتاح API.');
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasAnyRole(['root', 'admin']), 403);
    }
}

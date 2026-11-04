<?php

namespace App\Http\Controllers\API\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Models\Website\WebsiteSetting;
use App\Repositories\SettingRepository;

class SettingController extends Controller
{
    /**
     * Map legacy legal-page slugs (used by the mobile app) to the
     * WebsiteSetting keys edited by the super admin in "صفحات الموقع".
     */
    public const PAGE_KEY_MAP = [
        'privacy-policy'   => 'privacy_policy',
        'trams-of-service' => 'terms_conditions',
        'terms-conditions'  => 'terms_conditions',
        'about-us'         => 'about_us',
        'contact-us'       => 'contact_us',
    ];

    public function show(Setting $page)
    {
        // Prefer super-admin content (website_settings) so edits in the
        // dashboard appear in the app immediately. Fall back to the legacy
        // settings row to preserve current app data when nothing was saved yet.
        $sitePage = $this->sitePageResponse($page->slug);
        if ($sitePage !== null) {
            return response()->json($sitePage);
        }

        return $this->json($page->title, [
            'setting' => new SettingResource($page)
        ]);
    }

    /**
     * All site pages at once (terms / privacy / contact / about),
     * including contact phone + address. Used by the app to stay in sync
     * with the super-admin "صفحات الموقع" section in one call.
     */
    public function sitePages()
    {
        return response()->json([
            'message' => 'Site pages',
            'data' => [
                'terms_conditions' => $this->pageData('terms_conditions'),
                'privacy_policy'   => $this->pageData('privacy_policy'),
                'contact_us'       => $this->pageData('contact_us'),
                'about_us'         => $this->pageData('about_us'),
            ],
        ]);
    }

    /**
     * Build the app-facing payload for a WebsiteSetting key, or null when
     * the super admin has not saved anything yet (caller falls back).
     */
    protected function sitePageResponse(string $slug): ?array
    {
        $key = self::PAGE_KEY_MAP[$slug] ?? null;
        if ($key === null) {
            return null;
        }

        $data = $this->pageData($key);
        if ($data === null) {
            return null;
        }

        return [
            'message' => $data['title'] ?? $key,
            'data' => $data,
        ];
    }

    protected function pageData(string $key): ?array
    {
        $setting = WebsiteSetting::where('key', $key)->first();
        $value = $setting?->value ?? [];

        $title = trim((string) ($value['title'] ?? ''));
        $content = trim((string) ($value['content'] ?? ''));

        // Contact page also carries phone + address so the app can take the
        // fixed number from the super admin instead of hardcoded values.
        $extra = [];
        if ($key === 'contact_us') {
            $extra = $this->contactDetails($value);
        }

        if ($title === '' && $content === '' && empty($extra['phone_number']) && empty($extra['address'])) {
            return null;
        }

        return array_merge([
            'setting' => [
                'title' => $title !== '' ? $title : null,
                'content' => $content !== '' ? $content : null,
            ],
        ], $extra);
    }

    protected function contactDetails(array $contactValue): array
    {
        $phone = trim((string) ($contactValue['phone_number'] ?? ''));
        $address = trim((string) ($contactValue['address'] ?? ''));

        // Fall back to the footer contact block when the page block is empty.
        if ($phone === '' && $address === '') {
            $footer = WebsiteSetting::where('key', 'footer')->first()?->value ?? [];
            $footerContact = $footer['contact_us'] ?? [];
            $phone = trim((string) ($footerContact['phone_number'] ?? ''));
            $address = trim((string) ($footerContact['address'] ?? ''));
        }

        return [
            'phone_number' => $phone !== '' ? $phone : null,
            'address' => $address !== '' ? $address : null,
        ];
    }

    public function privacyShow(){
        return view('settings.privacy-policy');

    }
    public function termsShow(Setting $page){
        return $this->json($page->title, [
            'setting' => new SettingResource($page)
        ]);
    }
}

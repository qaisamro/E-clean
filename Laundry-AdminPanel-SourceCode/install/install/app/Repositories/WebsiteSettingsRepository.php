<?php


namespace App\Repositories;

use App\Http\Requests\Website\WebsiteSettingsRequest;
use App\Models\Website\WebsiteSetting;
use App\Repositories\MediaRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WebsiteSettingsRepository extends Repository
{
    public function model()
    {
        return WebsiteSetting::class;
    }

    public function index()
    {
        return $this->getAll()->keyBy('key');
    }

    public function firstOrCreateByKey(string $key, array $value = []): WebsiteSetting
    {
        return $this->query()->firstOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function updateOrCreateByKey(string $key, array $value): WebsiteSetting
    {
        return $this->query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function findByKey(string $key): ?WebsiteSetting
    {
        return $this->query()->where('key', $key)->first();
    }

    public function updateValue(WebsiteSetting $setting, array $value): WebsiteSetting
    {
        $setting->update(['value' => $value]);
        Cache::forget('website_settings');
        return $setting->fresh();
    }

    public function updateSingleImage(
        WebsiteSetting $setting,
        $file,
        array $data,
        string $key,
        string $path,
        MediaRepository $mediaRepo
    ): array {
        $media = !empty($data["{$key}_media_id"])
            ? $mediaRepo->find($data["{$key}_media_id"])
            : null;

        $media = $mediaRepo->updateOrCreateByRequest($file, $path, 'Image', $media);

        $data[$key] = $media->src;
        $data["{$key}_media_id"] = $media->id;

        return $data;
    }

    public function updateImageGroup(
        WebsiteSetting $setting,
        array $items,
        string $path,
        MediaRepository $mediaRepo
    ): array {
        return collect($items)->map(function ($item) use ($path, $mediaRepo) {

            if (!empty($item['img']) && $item['img'] instanceof \Illuminate\Http\UploadedFile) {

                $media = !empty($item['media_id'])
                    ? $mediaRepo->find($item['media_id'])
                    : null;

                $media = $mediaRepo->updateOrCreateByRequest(
                    $item['img'],
                    $path,
                    'Image',
                    $media
                );

                return [
                    'img' => $media->src,
                    'media_id' => $media->id,
                ];
            }

            return !empty($item['img'])
                ? [
                    'img' => $item['img'],
                    'media_id' => $item['media_id'] ?? null,
                ]
                : null;
        })->filter()->values()->toArray();
    }

    public function updateHeader(WebsiteSettingsRequest $request, MediaRepository $mediaRepo)
    {
        return DB::transaction(function () use ($request, $mediaRepo) {
            $setting = $this->firstOrCreateByKey('header', []);

            $header = $setting->value ?? [];

            if ($request->hasFile('header_img')) {
                $header = $this->updateSingleImage(
                    $setting,
                    $request->file('header_img'),
                    $header,
                    'header_img',
                    'headers',
                    $mediaRepo
                );
            }

            $header['trusted_client_image_group'] = $this->updateImageGroup(
                $setting,
                $request->trusted_client_image_group ?? [],
                'headers/trusted',
                $mediaRepo
            );

            $this->updateValue($setting, array_merge(
                $header,
                $request->only('title', 'description')
            ));

            return $setting->fresh()->value;
        });
    }

    public function updatePremiumServices(WebsiteSettingsRequest $request)
    {
        $setting = $this->updateOrCreateByKey('premium_services', [
            'title' => $request->title,
            'sub_title' => $request->sub_title,
        ]);

        Cache::forget('website_settings');

        return $setting->fresh()->value;
    }

    public function updateExperienceServices(WebsiteSettingsRequest $request)
    {
        $setting = $this->updateOrCreateByKey('experience_services', [
            'title' => $request->title,
            'sub_title' => $request->sub_title,
        ]);

        Cache::forget('website_settings');

        return $setting->fresh()->value;
    }

    public function updateHowItWorks(WebsiteSettingsRequest $request, MediaRepository $mediaRepo)
    {
        return DB::transaction(function () use ($request, $mediaRepo) {
            $setting = $this->firstOrCreateByKey('how_it_works', []);

            $data = $setting->value ?? [];

            if ($request->hasFile('right_side_img')) {
                $data = $this->updateSingleImage(
                    $setting,
                    $request->file('right_side_img'),
                    $data,
                    'right_side_img',
                    'how-it-work',
                    $mediaRepo
                );
            }

            $data['work_steps'] = $request->work_steps ?? $data['work_steps'] ?? [];
            $data['title'] = $request->title ?? ($data['title'] ?? '');

            $this->updateValue($setting, $data);

            return $setting->fresh()->value;
        });
    }

    public function updateBuildOnTrust(WebsiteSettingsRequest $request, MediaRepository $mediaRepo)
    {
        return DB::transaction(function () use ($request, $mediaRepo) {
            $setting = $this->firstOrCreateByKey('build_on_trust', []);

            $data = $setting->value ?? [];

            $data['title'] = $request->title ?? ($data['title'] ?? '');
            $data['sub_title'] = $request->sub_title ?? ($data['sub_title'] ?? '');

            $samples = $request->sample ?? [];

            foreach ($samples as $index => &$sample) {
                if (!empty($sample['icon']) && $sample['icon'] instanceof \Illuminate\Http\UploadedFile) {
                    $sample = $this->updateSingleImage(
                        $setting,
                        $sample['icon'],
                        $sample,
                        'icon',
                        'build-on-trust',
                        $mediaRepo
                    );
                }
            }
            $data['sample'] = $samples;

            $this->updateValue($setting, $data);

            return $setting->fresh()->value;
        });
    }

    public function updateOurPromise(WebsiteSettingsRequest $request, MediaRepository $mediaRepo)
    {
        return DB::transaction(function () use ($request, $mediaRepo) {
            $setting = $this->firstOrCreateByKey('our_promise', []);

            $data = $setting->value ?? [];

            if ($request->hasFile('background_image')) {
                $data = $this->updateSingleImage(
                    $setting,
                    $request->file('background_image'),
                    $data,
                    'background_image',
                    'our-promise',
                    $mediaRepo
                );
            }

            if ($request->hasFile('side_image')) {
                $data = $this->updateSingleImage(
                    $setting,
                    $request->file('side_image'),
                    $data,
                    'side_image',
                    'our-promise',
                    $mediaRepo
                );
            }

            $data['title'] = $request->title ?? ($data['title'] ?? '');
            $data['sub_title'] = $request->sub_title ?? ($data['sub_title'] ?? '');

            $this->updateValue($setting, $data);

            return $setting->fresh()->value;
        });
    }

    public function updateJoinOurNetwork(WebsiteSettingsRequest $request, MediaRepository $mediaRepo)
    {
        return DB::transaction(function () use ($request, $mediaRepo) {
            $setting = $this->firstOrCreateByKey('join_our_network', []);

            $data = $setting->value ?? [];

            $data['title'] = $request->title ?? ($data['title'] ?? '');
            $data['description'] = $request->description ?? ($data['description'] ?? '');
            $data['lists'] = $request->lists ?? $data['lists'] ?? [];

            $facilities = $request->facilities ?? [];
            foreach ($facilities as $index => &$facility) {
                if (!empty($facility['icon']) && $facility['icon'] instanceof \Illuminate\Http\UploadedFile) {
                    $facility = $this->updateSingleImage(
                        $setting,
                        $facility['icon'],
                        $facility,
                        'icon',
                        'join-our-network',
                        $mediaRepo
                    );
                }
            }
            $data['facilities'] = $facilities;

            $this->updateValue($setting, $data);

            return $setting->fresh()->value;
        });
    }

    public function updateTakeWithYou(WebsiteSettingsRequest $request, MediaRepository $mediaRepo)
    {
        $setting = $this->firstOrCreateByKey('take_with_you', []);

        $data = $setting->value ?? [];

        if ($request->hasFile('right_side_image')) {
            $data = $this->updateSingleImage(
                $setting,
                $request->file('right_side_image'),
                $data,
                'right_side_image',
                'take-with-us',
                $mediaRepo
            );
        }

        if ($request->has('image_group')) {
            $existingImages = $data['image_group'] ?? [];
            $imageGroup = $request->image_group;

            if (!empty($imageGroup)) {
                $updatedImages = $this->updateImageGroup(
                    $setting,
                    $imageGroup,
                    'take-with-us',
                    $mediaRepo
                );

                $data['image_group'] = $updatedImages ?: $existingImages;
            } else {
                $data['image_group'] = $existingImages;
            }
        }

        if ($request->has('infos') && is_array($request->infos)) {
            $existingInfos = $data['infos'] ?? [];
            $finalInfos = [];

            foreach ($request->infos as $index => $info) {
                $existing = $existingInfos[$index] ?? [];

                if (
                    isset($info['icon']) &&
                    $info['icon'] instanceof \Illuminate\Http\UploadedFile
                ) {
                    $info = $this->updateSingleImage(
                        $setting,
                        $info['icon'],
                        $info,
                        'icon',
                        'take-with-us',
                        $mediaRepo
                    );
                } elseif (isset($existing['icon'])) {
                    $info['icon'] = $existing['icon'];
                }

                $finalInfos[] = [
                    'icon'      => $info['icon'] ?? null,
                    'title'     => $info['title'] ?? '',
                    'sub_title' => $info['sub_title'] ?? '',
                ];
            }

            $data['infos'] = $finalInfos;
        }

        if ($request->has('button_group')) {
            $data['button_group'] = $request->button_group;
        }

        if ($request->has('take_info')) {
            $existingTakeInfo = $data['take_info'][0] ?? [];
            $takeInfo = $request->take_info;

            if (
                empty($takeInfo['icon']) &&
                isset($existingTakeInfo['icon'])
            ) {
                $takeInfo['icon'] = $existingTakeInfo['icon'];
            }

            if (
                !empty($takeInfo['icon']) &&
                $takeInfo['icon'] instanceof \Illuminate\Http\UploadedFile
            ) {
                $takeInfo = $this->updateSingleImage(
                    $setting,
                    $takeInfo['icon'],
                    $takeInfo,
                    'icon',
                    'take-with-us',
                    $mediaRepo
                );
            }

            $data['take_info'] = [$takeInfo];
        }

        if ($request->filled('title')) {
            $data['title'] = $request->title;
        }

        if ($request->filled('sub_title')) {
            $data['sub_title'] = $request->sub_title;
        }

        $this->updateValue($setting, $data);

        return $setting->fresh()->value;
    }

    public function updateGetStarted(WebsiteSettingsRequest $request)
    {
        $this->updateOrCreateByKey('get_started', [
            'title' => $request->title,
            'sub_title' => $request->sub_title,
        ]);

        Cache::forget('website_settings');

        return true;
    }

    public function updateFooter(WebsiteSettingsRequest $request, MediaRepository $mediaRepo)
    {
        return DB::transaction(function () use ($request, $mediaRepo) {
            $setting = $this->firstOrCreateByKey('footer', []);

            $data = $setting->value ?? [];

            if ($request->hasFile('footer_logo')) {
                $data = $this->updateSingleImage(
                    $setting,
                    $request->file('footer_logo'),
                    $data,
                    'footer_logo',
                    'footer',
                    $mediaRepo
                );
            }

            if ($request->hasFile('footer_background')) {
                $data = $this->updateSingleImage(
                    $setting,
                    $request->file('footer_background'),
                    $data,
                    'footer_background',
                    'footer',
                    $mediaRepo
                );
            }

            $oldFollowUs = $data['follow_us'] ?? [];
            $newFollowUs = [];

            if ($request->has('follow_us') && is_array($request->follow_us)) {
                foreach ($request->follow_us as $index => $item) {
                    $followItem = [
                        'link' => $item['link'] ?? '',
                        'icon' => $oldFollowUs[$index]['icon'] ?? null,
                    ];

                    if ($request->hasFile("follow_us.$index.icon")) {
                        $uploadedFile = $request->file("follow_us.$index.icon");

                        $followItem = $this->updateSingleImage(
                            $setting,
                            $uploadedFile,
                            $followItem,
                            'icon',
                            'footer',
                            $mediaRepo
                        );
                    }

                    $newFollowUs[] = $followItem;
                }
            }

            $data['follow_us'] = $newFollowUs;
            $data['footer_title'] = $request->footer_title ?? ($data['footer_title'] ?? '');
            $data['footer_left_side_text'] = $request->footer_left_side_text ?? ($data['footer_left_side_text'] ?? '');
            $data['footer_right_side_text'] = $request->footer_right_side_text ?? ($data['footer_right_side_text'] ?? '');
            $data['contact_us'] = $request->contact_us ?? ($data['contact_us'] ?? []);

            $this->updateValue($setting, $data);

            return $setting->fresh()->value;
        });
    }

    public function updateTermsConditions(WebsiteSettingsRequest $request)
    {
        $request->validate([
            'title' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        $this->updateOrCreateByKey('terms_conditions', [
            'title' => $request->title,
            'content' => $request->content,
        ]);

        Cache::forget('website_settings');

        return true;
    }

    public function updatePrivacyPolicy(WebsiteSettingsRequest $request)
    {
        $this->updateOrCreateByKey('privacy_policy', [
            'title' => $request->title,
            'content' => $request->content,
        ]);

        Cache::forget('website_settings');

        return true;
    }
}

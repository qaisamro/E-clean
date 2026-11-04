<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\WebsiteSettingsRequest;
use App\Repositories\MediaRepository;
use App\Repositories\WebsiteSettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebsiteController extends Controller
{

    public function index(WebsiteSettingsRepository $websiteRepo)
    {
        // Redirect vendor_admin users to their separate settings page
        if (auth()->check() && auth()->user()?->hasRole('vendor_admin')) {
            return redirect()->route('vendor.settings');
        }
        $webSettings = $websiteRepo->index();

        return view('website.pages.website', compact('webSettings'));
    }

    public function update(WebsiteSettingsRequest $request, string $type, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        return match ($type) {
            'header'              => $this->updateHeader($request, $websiteRepo, $mediaRepo),
            'premium_services'    => $this->updatePremiumServices($request, $websiteRepo),
            'experience_services' => $this->updateExperienceServices($request, $websiteRepo),
            'how_it_works'       => $this->updateHowItWorks($request, $websiteRepo, $mediaRepo),
            'build_on_trust'      => $this->updateBuildOnTrust($request, $websiteRepo, $mediaRepo),
            'our_promise'        => $this->updateOurPromise($request, $websiteRepo, $mediaRepo),
            'join_our_network'    => $this->updateJoinOurNetwork($request, $websiteRepo, $mediaRepo),
            'take_with_you'      => $this->updateTakeWithYou($request, $websiteRepo, $mediaRepo),
            'get_started'        => $this->updateGetStarted($request, $websiteRepo),
            'footer'              => $this->updateFooter($request, $websiteRepo, $mediaRepo),
            'terms_conditions'   => $this->updateTermsConditions($request, $websiteRepo),
            'privacy_policy'     => $this->updatePrivacyPolicy($request, $websiteRepo),
            'contact_us'         => $this->updateContactUs($request, $websiteRepo),
            'about_us'           => $this->updateAboutUs($request, $websiteRepo),
            default               => abort(400, 'Invalid form type'),
        };
    }

    public function updateHeader(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateHeader($request, $mediaRepo);

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function updatePremiumServices(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updatePremiumServices($request);

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function updateExperienceServices(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updateExperienceServices($request);

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function updateHowItWorks(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateHowItWorks($request, $mediaRepo);

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function updateBuildOnTrust(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateBuildOnTrust($request, $mediaRepo);

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function updateOurPromise(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateOurPromise($request, $mediaRepo);

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function updateJoinOurNetwork(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateJoinOurNetwork($request, $mediaRepo);

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function updateTakeWithYou(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateTakeWithYou($request, $mediaRepo);

        return back()->with('success', 'تم تحديث قسم «خذه معنا» بنجاح.');
    }

    public function updateGetStarted(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updateGetStarted($request);

        return back()->with('success', 'تم تحديث قسم «ابدأ الآن» بنجاح.');
    }

    public function updateFooter(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateFooter($request, $mediaRepo);

        return back()->with('success', 'تم تحديث قسم التذييل بنجاح.');
    }

    public function updateTermsConditions(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updateTermsConditions($request);

        return back()->with('success', 'تم تحديث الشروط والأحكام بنجاح.');
    }

    public function updatePrivacyPolicy(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updatePrivacyPolicy($request);

        return back()->with('success', 'تم تحديث سياسة الخصوصية بنجاح.');
    }

    public function updateContactUs(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updateOrCreateByKey('contact_us', [
            'title' => $request->title,
            'content' => $request->content,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);
        Cache::forget('website_settings');
        return back()->with('success', 'تم تحديث قسم التواصل معنا بنجاح.');
    }

    public function updateAboutUs(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updateOrCreateByKey('about_us', [
            'title' => $request->title,
            'content' => $request->content,
        ]);
        Cache::forget('website_settings');
        return back()->with('success', 'تم تحديث قسم المزيد عنا بنجاح.');
    }
}

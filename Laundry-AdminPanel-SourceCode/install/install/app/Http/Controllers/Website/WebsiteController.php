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
            default               => abort(400, 'Invalid form type'),
        };
    }

    public function updateHeader(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateHeader($request, $mediaRepo);

        return back()->with('success', 'Updated Successfully');
    }

    public function updatePremiumServices(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updatePremiumServices($request);

        return back()->with('success', 'Updated Successfully');
    }

    public function updateExperienceServices(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updateExperienceServices($request);

        return back()->with('success', 'Updated Successfully');
    }

    public function updateHowItWorks(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateHowItWorks($request, $mediaRepo);

        return back()->with('success', 'Updated Successfully');
    }

    public function updateBuildOnTrust(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateBuildOnTrust($request, $mediaRepo);

        return back()->with('success', 'Updated Successfully');
    }

    public function updateOurPromise(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateOurPromise($request, $mediaRepo);

        return back()->with('success', 'Updated Successfully');
    }

    public function updateJoinOurNetwork(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateJoinOurNetwork($request, $mediaRepo);

        return back()->with('success', 'Updated Successfully');
    }

    public function updateTakeWithYou(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateTakeWithYou($request, $mediaRepo);

        return back()->with('success', 'Take With Us section updated successfully.');
    }

    public function updateGetStarted(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updateGetStarted($request);

        return back()->with('success', 'Get Started section updated successfully.');
    }

    public function updateFooter(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo, MediaRepository $mediaRepo)
    {
        $websiteRepo->updateFooter($request, $mediaRepo);

        return back()->with('success', 'Footer section updated successfully.');
    }

    public function updateTermsConditions(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updateTermsConditions($request);

        return back()->with('success', 'Terms & Conditions updated successfully.');
    }

    public function updatePrivacyPolicy(WebsiteSettingsRequest $request, WebsiteSettingsRepository $websiteRepo)
    {
        $websiteRepo->updatePrivacyPolicy($request);

        return back()->with('success', 'Privacy Policy updated successfully.');
    }
}

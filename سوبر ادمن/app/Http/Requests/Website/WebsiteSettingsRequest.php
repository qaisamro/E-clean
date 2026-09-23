<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebsiteSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Get the type from route parameters
        $type = $this->route('type');

        return match ($type) {
            'header' => $this->headerRules(),
            'premium_services' => $this->premiumServicesRules(),
            'experience_services' => $this->experienceServicesRules(),
            'how_it_works' => $this->howItWorksRules(),
            'build_on_trust' => $this->buildOnTrustRules(),
            'our_promise' => $this->ourPromiseRules(),
            'join_our_network' => $this->joinOurNetworkRules(),
            'take_with_you' => $this->takeWithYouRules(),
            'get_started' => $this->getStartedRules(),
            'footer' => $this->footerRules(),
            'terms_conditions' => $this->termsConditionsRules(),
            'privacy_policy' => $this->privacyPolicyRules(),
            default => [],
        };
    }

    /**
     * Header validation rules
     */
    protected function headerRules(): array
    {
        return [
            'header' => 'nullable|string|max:255',
            'header_img' => 'nullable|image',
            'trusted_client_image_group.*.img' => 'nullable|image',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Premium Services validation rules
     */
    protected function premiumServicesRules(): array
    {
        return [
            'title' => 'nullable|string',
            'sub_title' => 'nullable|string',
        ];
    }

    /**
     * Experience Services validation rules
     */
    protected function experienceServicesRules(): array
    {
        return [
            'title' => 'nullable|string',
            'sub_title' => 'nullable|string',
        ];
    }

    /**
     * How It Works validation rules
     */
    protected function howItWorksRules(): array
    {
        return [
            'title' => 'nullable|string',
            'right_side_img' => 'nullable|image',
            'work_steps.*.number' => 'nullable|integer',
            'work_steps.*.title' => 'nullable|string',
            'work_steps.*.sub_title' => 'nullable|string',
        ];
    }

    /**
     * Build On Trust validation rules
     */
    protected function buildOnTrustRules(): array
    {
        return [
            'title' => 'nullable|string',
            'sub_title' => 'nullable|string',
            'sample.*.title' => 'nullable|string',
            'sample.*.description' => 'nullable|string',
            'sample.*.icon' => 'nullable|image',
        ];
    }

    /**
     * Our Promise validation rules
     */
    protected function ourPromiseRules(): array
    {
        return [
            'title' => 'nullable|string',
            'sub_title' => 'nullable|string',
            'background_image' => 'nullable|image',
            'side_image' => 'nullable|image',
        ];
    }

    /**
     * Join Our Network validation rules
     */
    protected function joinOurNetworkRules(): array
    {
        return [
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'lists.*.list' => 'nullable|string',
            'facilities.*.title' => 'nullable|string',
            'facilities.*.description' => 'nullable|string',
            'facilities.*.icon' => 'nullable|image',
        ];
    }

    /**
     * Take With You validation rules
     */
    protected function takeWithYouRules(): array
    {
        return [
            'right_side_image' => 'nullable|image',
            'image_group.*.img' => 'nullable|image',
            'infos.*.icon' => 'nullable|image',
            'take_info.icon' => 'nullable|image',
            'title' => 'nullable|string',
            'sub_title' => 'nullable|string',
            'take_info.title' => 'nullable|string',
            'take_info.sub_title' => 'nullable|string',
            'button_group.*.name' => 'nullable|string',
            'button_group.*.link' => 'nullable|string',
        ];
    }

    /**
     * Get Started validation rules
     */
    protected function getStartedRules(): array
    {
        return [
            'title' => 'nullable|string',
            'sub_title' => 'nullable|string',
        ];
    }

    /**
     * Footer validation rules
     */
    protected function footerRules(): array
    {
        return [
            'footer_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'footer_background' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'footer_title' => 'nullable|string',
            'footer_left_side_text' => 'nullable|string',
            'footer_right_side_text' => 'nullable|string',
            'contact_us' => 'nullable|array',
            'contact_us.address' => 'nullable|string',
            'contact_us.phone_number' => 'nullable|string',
            'follow_us' => 'nullable|array',
            'follow_us.*.link' => 'nullable|string',
            'follow_us.*.icon' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'follow_us.*.icon' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ];
    }

    /**
     * Terms & Conditions validation rules
     */
    protected function termsConditionsRules(): array
    {
        return [
            'title' => 'nullable|string',
            'content' => 'nullable|string',
        ];
    }

    /**
     * Privacy Policy validation rules
     */
    protected function privacyPolicyRules(): array
    {
        return [
            'title' => 'nullable|string',
            'content' => 'nullable|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'header_img.image' => 'The header image must be an image file.',
            'right_side_image.image' => 'The right side image must be an image file.',
            'background_image.image' => 'The background image must be an image file.',
            'side_image.image' => 'The side image must be an image file.',
            'footer_logo.image' => 'The footer logo must be an image file.',
            'footer_logo.mimes' => 'The footer logo must be a file of type: jpg, jpeg, png, webp, svg.',
            'footer_background.image' => 'The footer background must be an image file.',
            'footer_background.mimes' => 'The footer background must be a file of type: jpg, jpeg, png, webp, svg.',
        ];
    }
}

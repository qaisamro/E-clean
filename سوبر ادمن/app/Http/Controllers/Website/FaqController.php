<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Repositories\FaqRepository;
use App\Repositories\FaqsCategoryRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function website(FaqRepository $faqRepo, FaqsCategoryRepository $faqsCategoryRepo)
    {
        $faqs = $faqRepo->getAll();
        $categories = $faqsCategoryRepo->getActive();
        $services = (new ServiceRepository())->getAll(true);

        return view('website.pages.faq', compact('faqs', 'categories', 'services'));
    }
}

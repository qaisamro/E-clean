<?php

namespace App\Http\Controllers\Web\Social;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Repositories\SocialLinkRepository;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function index()
    {
        $socialLink = (new SocialLinkRepository())->getAll();
        return view('social.index',compact('socialLink'));
    }

    public function store(Request $request)
    {
        (New SocialLinkRepository())->storeByRequest($request);
        return redirect()->back()->with('success','تمت إضافة الرابط بنجاح');
    }

    public function update(Request $request, SocialLink $socialLink)
    {
        (New SocialLinkRepository())->updateByRequest($request,$socialLink);
        return redirect()->back()->with('success','تم تحديث الرابط بنجاح');
    }

    public function delete(SocialLink $socialLink)
    {
        (New SocialLinkRepository())->deleteByRequest($socialLink);
        return redirect()->back()->with('success','تم الحذف بنجاح');
    }
}

<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use App\Mail\ThankYouMail;
use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(ContactRepository $contactRepo)
    {
        $contact = $contactRepo->getAll()->first();
        return view('website.pages.contact', compact('contact'));
    }

    public function submit(ContactRequest $request, ContactRepository $contactRepo)
    {
        $contact = $contactRepo->storeByRequest($request);

        // Send thank you email to user
        Mail::to($contact->email)->send(new ThankYouMail($contact->name));

        return back()->with('success', 'Thanks for contacting us! We will get back to you soon.');
    }
}

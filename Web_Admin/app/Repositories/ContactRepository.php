<?php

namespace App\Repositories;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ContactRepository extends Repository
{
    public function model()
    {
        return Contact::class;
    }

    public function getAll()
    {
        return $this->query()->latest('id')->get();
    }

    public function storeByRequest(ContactRequest $request): Contact
    {
        $contact = $this->query()->create([
            'name' => $request->name,
            'phone_number' => $request->phone_number ?? '',
            'email' => $request->email,
            'message' => $request->message
        ]);
        return $contact;
    }
}

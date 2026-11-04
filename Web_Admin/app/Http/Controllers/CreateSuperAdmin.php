<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreateSuperAdmin extends Controller
{
    public function index()
    {
        return view('create-root');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $rootUser = User::factory()->create([
            'first_name' => 'Super Admin',
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'mobile' => '01000000001',
            'is_active' => true,
        ]);
        $permissions = config('acl.permissions');

        foreach ($permissions as $permission => $value) {
            $rootUser->givePermissionTo($permission);
        }
        $rootUser->assignRole('root');

        // Redirect to the dashboard or any other page
        return redirect()->route('login')->with('success', 'أنت جاهز لاستخدام ReadyPOS! يرجى تسجيل الدخول ببياناتك.');
    }
}

<?php

namespace App\Http\Controllers\Web\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdminLoginRequest as LoginRequest;
use App\Models\Setting;
use App\Models\User;
use App\Models\WebSetting;

class LoginController extends Controller
{


    public function index()
    {

        return view('auth.login');
    }

   public function login(LoginRequest $loginRequest)
    {

        $user = $this->checkDemoLogin($loginRequest);

       if (!$user) {
            $user = $this->isAuthenticate($loginRequest);
        }

        $loginRequest->only('email', 'password');

        if (!$user) {
            return redirect()->back()
                ->withErrors(['email' => ["Invalid credentials"]])
                ->withInput();
        }

         Auth::login($user);
         return redirect('/dashboard');
    }

    // private function checkDemoLogin($loginRequest)
    // {

    //     $currentEnv = strtolower(config('app.env') ?? env('APP_ENV', 'production'));


    //     if ($currentEnv === 'production') {
    //         return false;
    //     }


    //     $isDemoAllowed = in_array($currentEnv, ['local', 'demo'])
    //         && filter_var(config('app.show_demo_credentials') ?? env('SHOW_DEMO_CREDENTIALS', true), FILTER_VALIDATE_BOOLEAN);

    //     $demoEmail = config('app.demo_admin_email') ?? env('DEMO_ADMIN_EMAIL');
    //     $demoPassword = config('app.demo_admin_password') ?? env('DEMO_ADMIN_PASSWORD');

    //     if ($isDemoAllowed && filled($demoEmail) && filled($demoPassword)) {
    //         if ($loginRequest->email === $demoEmail && $loginRequest->password === $demoPassword) {

    //             $user = (new UserRepository())->findByContact($demoEmail);


    //             if (!$user) {
    //                 $user = User::where('is_active', 1)->first() ?? User::first();
    //             }

    //             if ($user && $user->is_active) {
    //                 return $user;
    //             }
    //         }
    //     }

    //     return false;
    // }


    private function checkDemoLogin($loginRequest)
{

    $isDemoModeActive = filter_var(config('services.demo_mode') ?? env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN);

    $demoEmail = config('app.demo_admin_email') ?? env('DEMO_ADMIN_EMAIL');
    $demoPassword = config('app.demo_admin_password') ?? env('DEMO_ADMIN_PASSWORD');


    if ($isDemoModeActive && filled($demoEmail) && filled($demoPassword)) {
        if ($loginRequest->email === $demoEmail && $loginRequest->password === $demoPassword) {

            $user = (new UserRepository())->findByContact($demoEmail);


            if (!$user) {
                $user = User::where('is_active', 1)->first() ?? User::first();
            }

            if ($user && $user->is_active) {
                return $user;
            }
        }
    }

    return false;
}


    private function isAuthenticate($loginRequest)
    {

        $user = (new UserRepository())->findByContact($loginRequest->email);
        if (!is_null($user) && $user->is_active && Hash::check($loginRequest->password, $user->password)) {
            return $user;
        }
        return false;
    }

    public function logout()
    {
        $user = auth()->user();
        Auth::logout($user);
         return redirect('/login');
    }

    public function privacyPolicy(){
        $websiteName = WebSetting::all();
        $setting = Setting::where('slug', 'privacy-policy')->first();
        return view('auth.privacy-policy',compact('setting','websiteName'));
    }
    public function termsCondition(){
        $websiteName = WebSetting::all();
        $setting = Setting::where('slug', 'trams-of-service')->first();
        return view('auth.terms-condition',compact('setting','websiteName'));
    }
}

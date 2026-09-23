<?php

namespace App\Http\Controllers\Website\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\LoginRequest;
use App\Http\Requests\Website\RegistrationRequest;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('website.pages.register');
    }

    public function register(RegistrationRequest $request, UserRepository $userRepository)
    {
        $user = $userRepository->registerUser($request);
        Auth::login($user);
        return redirect()->route('web.home')->with('success', 'Account created successfully!');
    }

    public function showLogin()
    {
        $request = request();
        $service_id = $request->query('service_id', null);
        return view('website.pages.sign-in', compact('service_id'));
    }

    public function login(LoginRequest $request)
    {
        $isDemoModeActive = filter_var(config('services.demo_mode') ?? env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN);
        $demoUserEmail = config('app.demo_user_email') ?? env('DEMO_USER_EMAIL');
        $demoUserPassword = config('app.demo_user_password') ?? env('DEMO_USER_PASSWORD');

        if ($isDemoModeActive && $request->email === $demoUserEmail && $request->password === $demoUserPassword) {

            $user = User::where('email', $demoUserEmail)->first();


            if (!$user) {
                $user = User::first();
            }

            if ($user) {
                Auth::login($user);
                return redirect()->route('web.home');
            }
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        $service_id = $request->service_id;

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if($service_id) {
                return redirect()->route('web.cart', ['service_id' =>  $service_id])->with('success', 'Logged in Successfully');
            }

            return redirect()->route('web.home')->with('success', 'Logged in Successfully');
        }

        return back()
            ->withErrors([
                'email' => 'Invalid credentials.',
            ])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('web.login');
    }
}

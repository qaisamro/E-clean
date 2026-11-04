<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DemoProtectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */



public function handle(Request $request, Closure $next)
{

    $isDemoModeActive = filter_var(config('services.demo_mode', false), FILTER_VALIDATE_BOOLEAN);

    if ($isDemoModeActive === true) {

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {


            if (
                $request->is('*admin/login*') ||
                 $request->is('/') ||
                $request->is('*login*') ||
                $request->is('*admin/logout*') ||
                $request->is('*logout*') ||
                $request->is('*pos*') ||
                $request->is('*fetch*') ||
                $request->is('*cart*') ||
                $request->is('*checkout*') ||
                $request->is('*order*') ||
                $request->is('*payment*') ||
                $request->is('*address*') ||
                $request->routeIs('*login*') ||
                $request->routeIs('*cart*') ||
                $request->routeIs('*checkout*') ||
                $request->routeIs('*order*') ||
                $request->routeIs('*address*') ||
                $request->routeIs('checkout.placeOrder') ||
                $request->routeIs('/')
            ) {
                return $next($request);
            }


          if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Action performed successfully (Demo Mode).'
                ], 200);
            }

            return redirect()->back()
                ->with('toast_success', 'Action performed successfully (Demo Mode).')
                ->with('success', 'Action performed successfully (Demo Mode).')
                ->withInput();

        }
    }

    return $next($request);
}


}

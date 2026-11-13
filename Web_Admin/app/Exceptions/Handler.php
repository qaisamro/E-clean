<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * AJAX / JSON requests get clean JSON failures — the sub-vendor screens
     * (and drivers) never receive a bare HTML error page that would crash
     * the JS error handlers and swallow the failure silently.
     */
    public function render($request, Throwable $exception)
    {
        if (($request->ajax() || $request->wantsJson()) && $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            return response()->json([
                'message' => 'غير مصرح — هذه العملية تحتاج صلاحية من مالك المتجر أو الإدارة.',
            ], 403);
        }

        if (($request->ajax() || $request->wantsJson()) && $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json([
                'message' => 'المورد غير موجود أو خارج النطاق المسموح لك.',
            ], 404);
        }

        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

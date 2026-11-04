<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\MediaRepository;
use App\Repositories\WebSettingRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class WebSettingController extends Controller
{
    public function index(WebSettingRepository $webSettingRepo)
    {
        $websetting = $webSettingRepo->index();
        $zones = $webSettingRepo->getAllZones();

        return view('web-setting', compact('websetting', 'zones'));
    }

    public function update(Request $request, WebSettingRepository $webSettingRepo, MediaRepository $mediaRepo, $webSettingId = null)
    {
        $webSetting = $webSettingId ? $webSettingRepo->find($webSettingId) : null;

        $webSettingRepo->updateOrCreate($request, $webSetting, $mediaRepo);

        if (config('app.timezone') != $request->timezone) {
            $this->setEnv('APP_TIMEZONE', $request->timezone);
        }

        if (config('app.currency_position') != $request->currency_position) {
            $this->setEnv('CURRENCY_POSITION', $request->currency_position);
        }

        Artisan::call('optimize:clear');
        Artisan::call('config:cache');

        return back()->with('success', 'تم التحديث بنجاح');
    }

    protected function setEnv($key, $value): bool
    {
        try {
            $envFile = app()->environmentFilePath();
            $str = file_get_contents($envFile);

            $keyPosition = strpos($str, "{$key}=");
            $endOfLinePosition = strpos($str, "\n", $keyPosition);
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

            $str = str_replace($oldLine, "{$key}={$value}", $str);

            $str = substr($str, 0, -1);
            $str .= "\n";

            file_put_contents($envFile, $str);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

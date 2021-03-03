<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Setting;
use App\Models\Upload;
use App\Helpers\Files;

class ConfigController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $maxDiskQuota = Setting::get('disk.max_disk_quota');
        return view('pages.config', [
            'defaultTheme' => Setting::get('app.default_theme'),
            'registrations' => boolval(Setting::get('app.registrations')),
            'useCaptcha' => boolval(Setting::get('app.captcha')),
            'maxDiskQuota' => $maxDiskQuota,
            'maxDiskQuotaShift' => Files::bytesToUnit(intval($maxDiskQuota)),
        ]);
    }

    public function editUser($userId)
    {
        $user = User::ofId($userId);

        if ($userId == 1 && Auth::user()->id != $userId) {
            flash(__('toast.error.edit.super'))->error();
            return back();
        }

        $maxDiskQuota = $user->getEffectiveMaxDiskQuota();
        return view('user.edit', [
            'user' => $user,
            'self' => Auth::user()->id == $userId,
            'maxDiskQuota' => $maxDiskQuota,
            'maxDiskQuotaShift' => Files::bytesToUnit(intval($maxDiskQuota))
        ]);
    }

    public function updateConfig()
    {
        request()->validate([
            "app_captcha" => ['numeric', Rule::in(['0', '1'])],
            "app_default-theme" => Rule::in(['dark', 'light']),
            "app_locale" => Rule::in(array_keys(Config::get('app.locales'))),
            "app_registrations" => ['numeric', Rule::in(['0', '1'])],
            "disk_max-quota" => ['numeric'],
            "disk_max-quota_unit" => ['numeric', Rule::in(['0', '10', '20', '30'])],
            "key_captcha_site" => ['required_if:app_captcha,1'],
            "key_captcha_private" => ['required_if:app_captcha,1'],
        ], [
            "key_captcha_site.required_if" => __("config.captcha.site.required"),
            "key_captcha_private.required_if" => __("config.captcha.private.required"),
        ]);

        Setting::set('app.captcha', request('app_captcha'));
        Setting::set('app.default_theme', request('app_default-theme'));
        Setting::set('app.locale', request('app_locale'));
        Setting::set('app.registrations', (bool) request('app_registrations'));
        Setting::set('disk.max_disk_quota', strval(intval(request('disk_max-quota')) << intval(request('disk_max-quota_unit'))));
        Setting::set('key.captcha.site', request('key_captcha_site'));
        Setting::set('key.captcha.private', request('key_captcha_private'));

        flash(__('config.success', [], request('app_locale')))->success();
        return back();
    }

    public function promoteUser($userId)
    {
        if ($userId == Auth::user()->id) {
            return response()->json([
                "message" => __('toast.error.promote.self')
            ], 403);
        }

        $user = User::ofId($userId);
        $user->admin = true;
        $user->save();
        return response()->json([
            "message" => __('toast.message.promote', ['username' => $user->username]),
        ], 200);
    }

    public function demoteUser($userId)
    {
        if ($userId == Auth::user()->id) {
            return response()->json([
                "message" => __('toast.error.demote.self')
            ], 403);
        }

        if ($userId == 1) {
            return response()->json([
                "message" => __('toast.error.demote.super')
            ], 403);
        }

        $user = User::ofId($userId);
        $user->admin = false;
        $user->save();
        return response()->json([
            "message" => __('toast.message.demote', ['username' => $user->username]),
        ], 200);
    }

    public function deleteUser($userId)
    {
        if ($userId == Auth::user()->id) {
            return response()->json([
                "message" => __('toast.error.delete.self')
            ], 403);
        }

        if ($userId == 1) {
            return response()->json([
                "message" => __('toast.error.delete.super')
            ], 403);
        }

        $user = User::ofId($userId);
        $user->delete();
        return response()->json([
            "message" => __('toast.message.delete', ['username' => $user->username]),
            "count" => trans_choice('config.infos.users', User::count()),
        ]);
    }

    public function createUser()
    {
        $register = new RegisterController;

        $register->validator(request()->all())->validate();
        $register->create(request()->all());
        flash(__('config.user.success'))->success();
        return back();
    }

    public function cleanUp()
    {
        $count = 0;
        foreach (Upload::all() as $file) {
            if (!Files::exists($file)) {
                if ($file->delete() && Storage::disk('public')->delete($file->path())) {
                    $count++;
                }
            }
        }

        foreach (Storage::disk('public')->allFiles() as $file) {
            $array = explode('/', $file);
            if (count($array) != 2) continue;
            if (!Upload::where('media_code', $array[1])->exists()) {
                if (Storage::disk('public')->delete($file)) {
                    $count++;
                }
            }
        }

        if ($count > 0) {
            return response()->json([
                "message" => trans_choice('toast.message.cleaned-orphaned', $count),
                "new_file_count" => trans_choice('config.infos.files', Upload::count()),
            ]);
        }

        return response()->json([
            "message" => __('toast.message.no-orphaned'),
            "new_file_count" => trans_choice('config.infos.files', Upload::count()),
        ]);
    }

    public function recalculateQuotas()
    {
        $users = User::all();
        $totalUsage = 0;

        foreach ($users as $user) {
            $user->disk_quota = 0;
        }
        foreach (Upload::all() as $upload) {
            $users->where('code', $upload->user_code)->first()->disk_quota += $upload->media_size;
            $totalUsage += $upload->media_size;
        }
        foreach ($users as $user) {
            $user->save();
        }

        $user = Auth::user()->refresh();
        $maxQuota = $user->getEffectiveMaxDiskQuota();
        return response()->json([
            "message" => __('toast.message.quotas'),
            "total_usage" => Files::humanFileSize($totalUsage),
            "new_usage" => $maxQuota > 0 ? $user->disk_quota / $maxQuota : 0,
            "new_quota" => Files::humanFileSize($user->disk_quota),
            "max_quota" => Files::humanFileSize($maxQuota),
            "unlimited_quota" => $maxQuota == 0,
        ]);
    }

    public function toggleMaintenance()
    {
        $currentState = Setting::get('app.maintenance');
        Setting::set('app.maintenance', !$currentState);
        return response()->json([
            "message" => __('toast.message.maintenance.' . (!$currentState ? "on" : "off")),
            "maintenance" => (bool) !$currentState,
        ]);
    }
}

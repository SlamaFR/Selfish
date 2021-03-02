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

        if ($user->super() && Auth::user()->id != $userId) {
            flash("You cannot edit superuser.")->error();
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
            "app_locale" => Rule::in(array_keys(Config::get('selfish.locales'))),
            "app_registrations" => ['numeric', Rule::in(['0', '1'])],
            "disk_max-quota" => ['numeric'],
            "disk_max-quota_unit" => ['numeric', Rule::in(['0', '10', '20', '30'])],
            "key_captcha_site" => ['required_if:app_captcha,1'],
            "key_captcha_private" => ['required_if:app_captcha,1'],
        ], [
            "key_captcha_site.required_if" => "The site key is required to enable reCAPTCHA.",
            "key_captcha_private.required_if" => "The private key is required to enable reCAPTCHA.",
        ]);

        Setting::set('app.captcha', request('app_captcha'));
        Setting::set('app.default_theme', request('app_default-theme'));
        Setting::set('app.locale', request('app_locale'));
        Setting::set('app.registrations', (bool) request('app_registrations'));
        Setting::set('disk.max_disk_quota', strval(intval(request('disk_max-quota')) << intval(request('disk_max-quota_unit'))));
        Setting::set('key.captcha.site', request('key_captcha_site'));
        Setting::set('key.captcha.private', request('key_captcha_private'));

        flash("Successfully updated config.")->success();
        return back();
    }

    public function promoteUser($userId)
    {
        if ($userId == Auth::user()->id) {
            return response()->json([
                "message" => "You cannot promote yourself."
            ], 403);
        }

        $user = User::ofId($userId);
        $user->admin = true;
        $user->save();
        return response()->json([
            "message" => "<strong>" . $user->username . "</strong> is now administrator."
        ], 200);
    }

    public function demoteUser($userId)
    {
        if ($userId == Auth::user()->id) {
            return response()->json([
                "message" => "You cannot demote yourself."
            ], 403);
        }

        if ($userId == 1) {
            return response()->json([
                "message" => "You cannot demote superuser."
            ], 403);
        }

        $user = User::ofId($userId);
        $user->admin = false;
        $user->save();
        return response()->json([
            "message" => "<strong>" . $user->username . "</strong> is no longer administrator."
        ], 200);
    }

    public function deleteUser($userId)
    {
        if ($userId == Auth::user()->id) {
            return response()->json([
                "message" => "You cannot delete yourself."
            ], 403);
        }

        if ($userId == 1) {
            return response()->json([
                "message" => "You cannot delete superuser."
            ], 403);
        }

        $user = User::ofId($userId);
        $user->delete();
        return response()->json([
            "message" => "Successfully deleted <strong>" . $user->username . "</strong>",
            "count" => User::count() . " users"
        ]);
    }

    public function createUser()
    {
        $register = new RegisterController;

        $register->validator(request()->all())->validate();
        $register->create(request()->all());
        flash("User successfully created.")->success();
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
                "message" => "Successfully removed " . $count . " orphaned files. You may recalculate quotas now.",
                "new_file_count" => Upload::count() . " files",
            ]);
        }

        return response()->json([
            "message" => "No orphaned file found.",
            "new_file_count" => Upload::count() . " files",
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
            "message" => "Successfully recalculated quotas.",
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
            "message" => !$currentState ? "Selfish is now in maintenance mode." : "Selfish is now live.",
            "maintenance" => (bool) !$currentState,
        ]);
    }
}

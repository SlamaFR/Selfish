<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pages\ConfigController;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\Files;

class SettingsController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $maxDiskQuota = $user->getEffectiveMaxDiskQuota();
        return view('user.edit', [
            'user' => $user, 
            'self' => true,
            'maxDiskQuota' => $maxDiskQuota,
            'maxDiskQuotaShift' => Files::bytesToUnit(intval($maxDiskQuota))
        ]);
    }

    public function updateInfo($userId = null)
    {
        $user = ($userId != null) ? User::ofId($userId) : Auth::user();

        if ($user->super() && Auth::user()->id != $userId) {
            flash('You cannot edit superuser.')->error();
            return back();
        }

        request()->validate([
            'username' => ['required', Rule::unique('users', 'username')->ignore($user->id), 'alpha'],
            'email' => ['required', Rule::unique('users', 'email')->ignore($user->id)],
            'disk_max-quota' => ['required', Rule::in(['default', 'custom'])],
            'disk_custom-max-quota' => ['numeric'],
            'disk_custom-max-quota_unit' => ['numeric', Rule::in(['0', '10', '20', '30'])],
            'disk_auto-delete' => ['boolean'],
        ]);

        $user->username = request('username');
        $user->email = request('email');

        if (Auth::user()->admin) {
            $user->settings()->set('disk.max_quota', request('disk_max-quota'));
            if (request('disk_max-quota') == 'custom') {
                $user->max_disk_quota = strval(intval(request('disk_custom-max-quota')) << intval(request('disk_custom-max-quota_unit')));
            } else {
                $user->max_disk_quota = "0";
            }
        }
        $user->settings()->set('disk.auto_delete', request('disk_auto-delete'));

        $user->save();
        flash('Successfully updated user settings.')->success();
        return back();
    }

    public function updatePassword($userId = null)
    {
        $user = ($userId != null) ? User::ofId($userId) : Auth::user();

        if ($user->super() && Auth::user()->id != $userId) {
            flash('You cannot edit superuser.')->error();
            return back();
        }

        request()->validate([
            'old_password' => ($userId != null) ? [] : ['required', new MatchOldPassword],
            'new_password' => ['required', 'confirmed', 'min:8']
        ]);

        $user->password = Hash::make(request('new_password'));
        $user->save();
        flash('Successfully updated password.')->success();
        return back();
    }

    public function updateDisplaySettings($userId = null)
    {
        $user = ($userId != null) ? User::ofId($userId) : Auth::user();
        $values = ['default', 'raw'];

        if ($user->super() && Auth::user()->id != $userId) {
            flash('You cannot edit superuser.')->error();
            return back();
        }

        request()->validate([
            "display_image" => [Rule::in($values)],
            "display_video" => [Rule::in($values)],
            "display_audio" => [Rule::in($values)],
            "display_text" => [Rule::in($values)],
            "display_pdf" => [Rule::in($values)],
            "display_file" => [Rule::in($values)],
        ]);

        foreach (request()->all() as $key => $value) {
            $key = str_replace('_', '.', $key);
            if ($user->settings()->has($key)) {
                $user->settings()->set($key, $value);
            }
        }
        $user->save();
        flash('Successfully updated display settings.')->success();
        return back();
    }

    public function regenerateToken($userId = null)
    {
        $user = ($userId != null) ? User::ofId($userId) : Auth::user();

        if ($user->super() && Auth::user()->id != $userId) {
            return response()->json([
                "message" => "You cannot edit superuser."
            ], 403);
        }

        do {
            $token = Str::random(32);
        } while (User::where('access_token', '=', $token)->exists());

        $user->access_token = $token;
        $user->save();
        return response()->json([
            "message" => ($userId == null) ? "Your personnal access token has been regenerated and copied to clipboard." : "<strong>" . $user->username . "</strong>'s personnal access token has been regenerated and copied to clipboard.",
            "token" => $token
        ], 200);
    }

    public function ShareX()
    {
        $user = Auth::user();

        return response($user->sharexConfig(), 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename=' . $user->username . '.sxcu'
        ]);
    }
}

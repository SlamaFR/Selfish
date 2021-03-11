<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\User;
use App\Models\Setting;
use App\Helpers\Files;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function index()
    {
        $max_upload_size = Files::humanFileSize(min(Files::stringToBytes(ini_get('upload_max_filesize')), Files::stringToBytes(ini_get('post_max_size'))));
        return view('pages.upload', [
            'max_upload_size' => $max_upload_size
        ]);
    }

    private function upload($user, $request)
    {
        do {
            $code = Str::random(10);
        } while (Upload::where('media_code', '=', $code)->exists());

        $file = $request->file('file');
        $fileSize = $file->getSize();
        $userMaxQuota = $user->getEffectiveMaxDiskQuota();
        $userQuota = $user->disk_quota;

        if ($userMaxQuota > 0) {
            if ($fileSize > $userMaxQuota) {
                return response()->json([
                    "error" => __('upload.error.too-big')
                ], 403);
            }
            if ($fileSize + $userQuota > $userMaxQuota) {
                if ($user->settings()->get('disk.auto_delete', '0') == '1') {
                    do {
                        $media = $user->uploads->shift();
                        $this->delete($media->media_code);
                    } while ($fileSize + $user->refresh()->disk_quota > $userMaxQuota);
                } else {
                    return response()->json([
                        "error" => __('upload.error.quota')
                    ], 403);
                }
            }
        }

        Storage::disk('public')->put($user->code . '/' . $code, file_get_contents($file));
        $media = Upload::create([
            'media_code' => $code,
            'media_name' => $file->getClientOriginalName(),
            'media_size' => Storage::disk('public')->size($user->code . '/' . $code),
            'media_type' => $file->getClientMimeType(),
            'user_code' => $user->code,
            'visible' => true
        ]);

        $user->disk_quota = $user->disk_quota + $media->media_size;
        $user->save();

        return response()->json([
            "status" => 200,
            "media_code" => $code,
            "url" => $media->url(),
        ]);
    }

    public function uploadMedia()
    {
        $user = Auth::user();
        return $this->upload($user, request());
    }

    public function uploadMediaToken($token)
    {
        $user = User::where('access_token', $token)->first();
        if ($user === null) {
            return response()->json([
                "error" => __('upload.error.token')
            ], 401);
        }

        return $this->upload($user, request());
    }

    public function toggleVisibility($mediaCode)
    {
        $upload = Upload::where('media_code', $mediaCode)->first();

        if ($upload === null) {
            return response()->json([
                "title" => __('general.error'),
                "message" => __('toast.error.not-found')
            ], 404);
        }

        if (!Auth::user()->admin && $upload->user_code != Auth::user()->code) {
            return response()->json([
                "title" => __('general.error'),
                "message" => __('toast.error.permission')
            ], 403);
        }

        $upload->visible = !$upload->visible;
        $upload->save();
        return response()->json([
            "title" => __('general.info'),
            "visible" => (int) $upload->visible,
            "btnIcon" => $upload->visible ? "eye-off" : "eye",
            "stateIcon" => $upload->visible ? "check-circle" : "x-circle",
            "stateColor" => $upload->visible ? "text-success" : "text-danger",
            "message" => __('toast.message.' . ($upload->visible ? "visible" : "invisible"), ['name' => $upload->media_name])
        ], 200);
    }

    public function delete($mediaCode)
    {
        $upload = Upload::where('media_code', $mediaCode)->first();

        if ($upload == null) {
            return response()->json([
                "title" => __('general.error'),
                "message" => __('toast.error.not-found')
            ], 404);
        }

        $user = $upload->owner;
        $maxQuota = $user->getEffectiveMaxDiskQuota();

        if (!$user->admin && $upload->user_code != $user->code) {
            return response()->json([
                "title" => __('general.error'),
                "message" => __('toast.error.permission')
            ], 403);
        }

        Storage::disk('public')->delete($upload->path());
        $user->disk_quota = $user->disk_quota - $upload->media_size;
        $user->save();
        $upload->delete();

        if (request()->getMethod() == 'GET') {
            return redirect('/files');
        }

        return response()->json([
            'new_quota' => Files::humanFileSize($user->disk_quota),
            'max_quota' => Files::humanFileSize($maxQuota),
            'new_usage' => $maxQuota > 0 ? $user->disk_quota / $maxQuota : 0,
            'unlimited_quota' => $maxQuota == 0,
        ]);
    }
}

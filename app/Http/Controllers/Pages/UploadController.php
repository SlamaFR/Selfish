<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\User;
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

        Storage::disk('public')->put($user->code . '/' . $code, file_get_contents($request->file('file')));
        $media = Upload::create([
            'media_code' => $code,
            'media_name' => $request->file('file')->getClientOriginalName(),
            'media_size' => Storage::disk('public')->size($user->code . '/' . $code),
            'user_code' => $user->code,
            'visible' => true
        ]);

        return '{
            "status": 200,
            "media_code": "' . $code . '", 
            "url": "' . $media->url() . '"
        }';
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
            return response('{
                "status": 401,
                "error": "The provided token does not exist."
            }', 401);
        }

        return $this->upload($user, request());
    }

    public function toggleVisibility($mediaCode)
    {
        $upload = Upload::where('media_code', $mediaCode)->first();

        if ($upload == null) {
            return abort(404);
        }

        if (!Auth::user()->admin && $upload->user_code != Auth::user()->code) {
            return abort(401);
        }

        $upload->visible = !$upload->visible;
        return $upload->save();
    }

    public function delete($mediaCode)
    {
        $upload = Upload::where('media_code', $mediaCode)->first();
        $user = Auth::user();

        if ($upload == null) {
            return abort(404);
        }

        if (!$user->admin && $upload->user_code != $user->code) {
            return abort(401);
        }

        Storage::disk('public')->delete($upload->path());
        return $upload->delete();
    }

    // public function deleteToken($mediaCode, $token)
    // {
    //     $upload = Upload::where('media_code', $mediaCode)->first();
    //     $user = User::where('access_token', $token)->firstOrFail();

    //     if ($upload == null) {
    //         return abort(404);
    //     }

    //     if (!$user->admin && $upload->user_code != $user->code) {
    //         return abort(401);
    //     }

    //     Storage::disk('public')->delete($upload->path());
    //     return $upload->delete();
    // }
}

<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Upload;
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

    public function uploadMedia()
    {
        do {
            $code = Str::random(10);
        } while (Upload::where('media_code', '=', $code)->exists());

        Storage::disk('public')->put(Auth::user()->code . '/' . $code, file_get_contents(request('file')));
        Upload::create([
            'media_code' => $code,
            'media_name' => request('file')->getClientOriginalName(),
            'user_code' => Auth::user()->code,
            'visible' => true
        ]);
    }

    public function toggleVisibility($mediaId)
    {
        $upload = Upload::where('id', '=', $mediaId)->first();

        if ($upload == null) {
            return abort(404);
        }

        if (!Auth::user()->admin() && $upload->user_code != Auth::user()->code) {
            return abort(403);
        }

        $upload->visible(!$upload->visible());
        return $upload->save();
    }

    public function delete($mediaId)
    {
        $upload = Upload::where('id', '=', $mediaId)->first();

        if ($upload == null) {
            return abort(404);
        }

        if (!Auth::user()->admin() && $upload->user_code != Auth::user()->code) {
            return abort(403);
        }

        Storage::disk('public')->delete($upload->path());
        return $upload->delete();
    }
}

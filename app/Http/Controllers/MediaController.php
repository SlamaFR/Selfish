<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Helpers\Files;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function download($userCode, $mediaCode)
    {
        $file = Upload::of($userCode, $mediaCode);

        if (!$file->visible()) {
            if (Auth::guest()) return abort(404);
            if (!Auth::user()->admin() && Auth::user()->code !== $file->user_code) return abort(404);
        }

        return Storage::disk('public')->download($file->path(), $file->media_name);
    }

    public function raw($userCode, $mediaCode)
    {
        $file = Upload::of($userCode, $mediaCode);

        if (!$file->visible()) {
            if (Auth::guest()) return abort(404);
            if (!Auth::user()->admin() && Auth::user()->code !== $file->user_code) return abort(404);
        }

        return Storage::disk('public')->response($file->path());
    }

    public function view($userCode, $mediaCode)
    {
        $file = Upload::of($userCode, $mediaCode);

        if (!$file->visible()) {
            if (Auth::guest()) return abort(404);
            if (!Auth::user()->admin() && Auth::user()->code !== $file->user_code) return abort(404);
        }

        $mimeType = Files::mimeType($file);

        return view('media', [
            'user_code' => $userCode,
            'media_code' => $mediaCode,
            'media_type' => Files::simplifyMimeType($mimeType),
            'mime_type' => $mimeType,
            'media_path' => route('media.raw', [
                'userCode' => $userCode,
                'mediaCode' => $mediaCode
            ]),
            'file' => $file,
            'media_raw' => Storage::disk('public')->get($file->path())
        ]);
    }
}

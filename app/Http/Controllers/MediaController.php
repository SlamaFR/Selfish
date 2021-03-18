<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Helpers\Files;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Facades\Agent;

class MediaController extends Controller
{
    public function download($mediaCode)
    {
        $file = Upload::of($mediaCode);

        if (!$file->visible) {
            if (Auth::guest()) return abort(404);
            if (!Auth::user()->admin && Auth::user()->code !== $file->user_code) return abort(404);
        }

        return Storage::disk('public')->download($file->path(), $file->media_name);
    }

    public function raw($mediaCode)
    {
        $file = Upload::of($mediaCode);

        if (!$file->visible) {
            if (Auth::guest()) return abort(404);
            if (!Auth::user()->admin && Auth::user()->code !== $file->user_code) return abort(404);
        }

        return Storage::disk('public')->response($file->path(), $file->media_name, [
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $file->media_type,
        ]);
    }

    public function view($mediaCode)
    {
        $file = Upload::of($mediaCode);

        if (!$file->visible) {
            if (Auth::guest()) return abort(404);
            if (!Auth::user()->admin && Auth::user()->code !== $file->user_code) return abort(404);
        }

        if (!Files::exists($file)) {
            return abort(404);
        }

        $type = Files::simplifyMimeType($file->media_type);

        if($file->owner->settings()->get("display." . $type) === 'raw' || Agent::isRobot()) {
            return $this->raw($mediaCode);
        }

        return view('media', [
            'user_code' => $file->user_code,
            'media_code' => $mediaCode,
            'media_type' => $type,
            'mime_type' => $file->media_type,
            'media_path' => route('media.raw', [
                'mediaCode' => $mediaCode
            ]),
            'file' => $file,
            'media_raw' => Storage::disk('public')->get($file->path())
        ]);
    }
}

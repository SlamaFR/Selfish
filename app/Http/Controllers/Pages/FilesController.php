<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Support\Facades\Auth;

class FilesController extends Controller
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
        $files = Upload::query();

        if (!Auth::user()->admin()) {
            $files->where('user_code', '=', Auth::user()->code);
        }

        if (request()->has('q')) {
            $files->where('media_name', 'like', '%' . strip_tags(request('q')) . '%');
        }

        return view('pages.files', [
            'files' => $files->paginate(25)
        ]);
    }
}

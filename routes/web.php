<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/files');
})->name('home');

Auth::routes();

Route::post('/mode/{mode}', function ($mode) {
    if ($mode == 'light' || $mode == 'dark') {
        Cookie::queue('theme', $mode, 525600);
    }
    return back();
})->name('mode');

Route::group([
    'middleware' => 'auth'
], function () {
    Route::get('/files', 'Pages\FilesController@index')->name('files');

    Route::get('/upload', 'Pages\UploadController@index')->name('upload');
    Route::post('/upload', 'Pages\UploadController@uploadMedia');
    Route::post('/upload/{mediaId}/toggle-visibility', 'Pages\UploadController@toggleVisibility');
    Route::post('/upload/{mediaId}/delete', 'Pages\UploadController@delete');

    Route::get('/config', 'Pages\ConfigController@index')->middleware('admin')->name('admin.config');

    Route::get('/user/settings', 'User\SettingsController@index')->name('user.settings');
    Route::post('/user/change-password', 'User\SettingsController@index');
});

Route::get('/{userCode}/{mediaCode}', 'MediaController@view')->name('media.view');
Route::get('/{userCode}/{mediaCode}/download', 'MediaController@download')->name('media.download');
Route::get('/{userCode}/{mediaCode}/raw', 'MediaController@raw')->name('media.raw');


/*
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');

// Logout Routes...
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('signup', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
*/

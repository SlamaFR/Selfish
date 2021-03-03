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
        Cookie::queue('theme', $mode, 5256000);
        return response()->json([
            "next_mode_icon" => $mode === 'dark' ? 'sun' : 'moon',
            "next_mode_name" => __('navbar.theme.' . $mode),
        ]);
    }
})->name('mode');

Route::group([
    'middleware' => 'auth'
], function () {
    Route::get('/files', 'Pages\FilesController@index')->name('files');

    Route::get('/upload', 'Pages\UploadController@index')->name('upload');
    Route::post('/upload', 'Pages\UploadController@uploadMedia');
    Route::post('/{mediaCode}/delete', 'Pages\UploadController@delete');
    Route::post('/{mediaCode}/toggle-visibility', 'Pages\UploadController@toggleVisibility');

    Route::group([
        'middleware' => 'admin'
    ], function () {
        Route::get('/admin', 'Pages\ConfigController@index')->name('admin');
        Route::get('/admin/user/{userId}/edit', 'Pages\ConfigController@editUser')->name('admin.user.edit');

        Route::post('/admin/update-config', 'Pages\ConfigController@updateConfig')->name('admin.config.update');

        Route::post('/admin/user/{userId}/delete', 'Pages\ConfigController@deleteUser')->name('admin.user.delete');
        Route::post('/admin/user/create', 'Pages\ConfigController@createUser')->name('admin.user.create');
        Route::post('/admin/user/{userId}/promote', 'Pages\ConfigController@promoteUser')->name('admin.user.promote');
        Route::post('/admin/user/{userId}/demote', 'Pages\ConfigController@demoteUser')->name('admin.user.demote');

        Route::post('/admin/user/{userId}/change-password', 'User\SettingsController@updatePassword')->name('admin.user.settings.password');
        Route::post('/admin/user/{userId}/change-info', 'User\SettingsController@updateInfo')->name('admin.user.settings.info');
        Route::post('/admin/user/{userId}/change-display', 'User\SettingsController@updateDisplaySettings')->name('admin.user.settings.display');
        Route::post('/admin/user/{userId}/regenerate-token', 'User\SettingsController@regenerateToken')->name('admin.user.token.regenerate');

        Route::post('/admin/recalculate-quotas', 'Pages\ConfigController@recalculateQuotas')->name('admin.quotas.recalculate');
        Route::post('/admin/clean-up', 'Pages\ConfigController@cleanUp')->name('admin.clean-up');
        Route::post('/admin/toggle-maintenance', 'Pages\ConfigController@toggleMaintenance')->name('admin.maintenance.toggle');
    });

    Route::get('/user/settings', 'User\SettingsController@index')->name('user.settings');
    Route::post('/user/change-password', 'User\SettingsController@updatePassword')->name('user.settings.password');
    Route::post('/user/change-info', 'User\SettingsController@updateInfo')->name('user.settings.info');
    Route::post('/user/change-display', 'User\SettingsController@updateDisplaySettings')->name('user.settings.display');
    Route::post('/user/regenerate-token', 'User\SettingsController@regenerateToken')->name('user.token.regenerate');
    Route::get('/user/external/sharex', 'User\SettingsController@ShareX')->name('user.external.sharex');
});

Route::get('/{mediaCode}', 'MediaController@view')->name('media.view');
Route::get('/{mediaCode}/download', 'MediaController@download')->name('media.download');
Route::get('/{mediaCode}/raw', 'MediaController@raw')->name('media.raw');

Route::post('/upload/{token}', 'Pages\UploadController@uploadMediaToken');
// Route::get('/{mediaCode}/delete/{token}', 'Pages\UploadController@deleteToken');

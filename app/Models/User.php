<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Support\Facades\Config;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasSettingsField;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'code',
        'password',
        'admin',
        'access_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'admin' => 'boolean'
    ];

    public static function of($userCode)
    {
        return User::where(['code' => $userCode])->firstOrFail();
    }

    public static function ofId($userId)
    {
        return User::where(['id' => $userId])->firstOrFail();
    }

    public function sharexConfig()
    {
        return '{
            "DestinationType": "ImageUploader, TextUploader, FileUploader",
            "RequestMethod": "POST",
            "RequestURL": "' . Config::get('app.url') .'/upload/' . $this->access_token . '",
            "Body": "MultipartFormData",
            "FileFormName": "file",
            "URL": "$json:url$",
            "ThumbnailURL": "$json:url$/raw",
            "ErrorMessage": "$json:error$"
          }';
    }
}

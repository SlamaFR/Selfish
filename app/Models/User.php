<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Support\Facades\Config;
use App\Helpers\Files;

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
        'access_token',
        'disk_quota',
        'max_disk_quota'
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
        'admin' => 'boolean',
        'disk_quota' => 'int',
        'max_disk_quota' => 'int'
    ];

    public static function of($userCode)
    {
        return User::where(['code' => $userCode])->firstOrFail();
    }

    public static function ofId($userId)
    {
        return User::where(['id' => $userId])->firstOrFail();
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class, 'user_code', 'code');
    }

    public function super()
    {
        return $this->id == 1;
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

    public function getEffectiveMaxDiskQuota()
    {
        if ($this->settings()->get('disk.max_quota') == 'default') {
            return intval(Setting::get('disk.max_disk_quota'));
        } else {
            return $this->max_disk_quota;
        }
    }
}

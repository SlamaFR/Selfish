<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_code',
        'media_name',
        'user_code',
        'visible'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'visible' => 'boolean'
    ];

    public function visible($visible = null)
    {
        if ($visible === null) {
            return $this->getAttribute('visible');
        }
        return $this->setAttribute('visible', $visible);
    }

    public function path()
    {
        return $this->user_code . '/' . $this->media_code;
    }

    public static function of($userCode, $mediaCode)
    {
        return Upload::where('user_code', $userCode)->where('media_code', $mediaCode)->firstOrFail();
    }
}

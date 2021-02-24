<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Files;

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
        'media_size',
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

    public function path()
    {
        return $this->user_code . '/' . $this->media_code;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_code', 'code');
    }

    public function url()
    {
        return route('media.view', ['mediaCode' => $this->media_code]);
    }

    public static function of($mediaCode)
    {
        return Upload::where('media_code', $mediaCode)->firstOrFail();
    }

    public function icon()
    {
        $type = Files::simplifyMimeType(Files::mimeType($this));
        switch ($type) {
            case 'font':
                return 'type';
            case 'audio':
                return 'music';
            case 'text':
            case 'pdf':
                return 'file-text';
            case 'zip':
                return 'archive';
            default:
                return $type;
        }
    }
}

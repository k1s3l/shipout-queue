<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmToken extends Model
{
    public const CODE_LIFETIME = 3;

    protected $fillable = ['code', 'token'];

    use HasFactory;

    public function setExpiredAtAttribute()
    {
         $this->attributes['expired_at'] = now()->addMinutes(self::CODE_LIFETIME);
    }
}

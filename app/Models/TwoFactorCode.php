<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TwoFactorCode extends Model
{
    protected $fillable = ['user_id', 'code', 'expires_at'];

    public static function generateCode()
    {
        return rand(100000, 999999);
    }

    public static function generateExpiration()
    {
        return now()->addMinutes(env('TWO_FACTOR_CODE_EXPIRATION', 1));
    }

    public function isExpired()
    {
        return Carbon::parse($this->expires_at)->lt(now());
    }
}

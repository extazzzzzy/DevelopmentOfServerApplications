<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'name',
        'description',
        'cipher'
    ];
    protected $dates = [
        'deleted_at'
    ];


    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($role) {
            $role->cipher = Str::uuid();
        });
    }
}

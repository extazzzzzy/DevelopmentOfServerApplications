<?php

namespace App\Models;

use App\Traits\LogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsChanges;

    protected $fillable = [
        'name',
        'description',
        'cipher',
        'created_by',
        'deleted_by'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->cipher = Str::uuid();
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($model) {
            $model->deleted_by = Auth::id();
            $model->save();
        });

        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_and_permissions');
    }
}

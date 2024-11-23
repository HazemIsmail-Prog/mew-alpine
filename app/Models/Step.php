<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Step extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->user_id = Auth::id();
        });
    }

}

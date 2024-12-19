<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Letter extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->user_id = Auth::id();
        });
    }

    public function creator() : BelongsTo {
        return $this->belongsTo(User::class ,'user_id');
    }

    public function getSuffixAttribute() : string {
        
        if($this->prefix == 'السادة'){
            return 'المحترمين';
        }
        
        return 'المحترم';
    }

}

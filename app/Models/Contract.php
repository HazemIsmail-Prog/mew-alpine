<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $guarded = [];

    public function children() : HasMany {
        return $this->hasMany(Contract::class);
    }

    public function users() : BelongsToMany {
        return $this->belongsToMany(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stakeholder extends Model
{
    protected $guarded = [];

    public function children(): HasMany
    {
        return $this->hasMany(Stakeholder::class);
    }
}

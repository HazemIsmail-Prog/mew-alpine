<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Info extends Model
{
    protected $guarded = [];
    protected $table = 'info';

    public function contract() : BelongsTo {
        return $this->belongsTo(Contract::class);
    }
}

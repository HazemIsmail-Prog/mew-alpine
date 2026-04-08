<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Action extends Model
{
    protected $guarded = [];
    protected $appends = [
        'can_delete',
        'can_edit',
    ];

    public function contract() : BelongsTo {
        return $this->belongsTo(Contract::class);
    }

    public function creator() : BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCanDeleteAttribute() : bool {
        return Auth::user()->role === 'superAdmin' || Auth::user()->role === 'admin' || $this->created_by === Auth::id();
    }

    public function getCanEditAttribute() : bool {
        return Auth::user()->role === 'superAdmin' || Auth::user()->role === 'admin' || $this->created_by === Auth::id();
    }
}

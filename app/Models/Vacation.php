<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Vacation extends Model
{
    protected $guarded = [];

    protected $appends = [
        'is_current',
        'is_future',
        'is_past',
        'formatted_start_date',
        'formatted_end_date',
        'can_delete',
        'can_update',
    ];
    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    public function getIsCurrentAttribute()
    {
        $today = now()->startOfDay();
        return $this->start_date->startOfDay() <= $today && $this->end_date->endOfDay() >= $today;
    }

    public function getIsFutureAttribute()
    {
        $today = now()->startOfDay();
        return $this->start_date->startOfDay() > $today;
    }

    public function getIsPastAttribute()
    {
        $today = now()->endOfDay();
        return $this->end_date->endOfDay() < $today;
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('Y-m-d');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->format('Y-m-d');
    }

    public function getCanDeleteAttribute()
    {
        return Auth::user()->role === 'superAdmin' || Auth::user()->role === 'admin';
    }

    public function getCanUpdateAttribute()
    {
        return Auth::user()->role === 'superAdmin' || Auth::user()->role === 'admin';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {

        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->is_completed = false;
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Stakeholder::class, 'from_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Stakeholder::class, 'to_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function uncompletedSteps(): HasMany
    {
        return $this->hasMany(Step::class)->where('is_completed', false);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
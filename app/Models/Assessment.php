<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assessment extends Model
{
    use HasFactory;

    const TYPE_GENERAL                  = 'general';
    const TYPE_PERSONALITY_CAREER_TEST  = 'personality_career_test';
    const TYPE_CAREER_STRENGTHS_FINDER  = 'career_strengths_finder';

    protected $fillable = ['user_id', 'type', 'date'];

    protected $casts = ['date' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(AssessmentResult::class);
    }
}

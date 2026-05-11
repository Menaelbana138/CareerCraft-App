<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'url', 'platform', 'skill_id'];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}

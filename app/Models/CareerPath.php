<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CareerPath extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'required_skills'];

    protected $casts = ['required_skills' => 'array'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_career_paths')
            ->withPivot(['status', 'progress'])
            ->withTimestamps();
    }
}

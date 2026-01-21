<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'world_id',
        'difficulty',
        'notes_included',
        'config',
    ];

    protected $casts = [
        'notes_included' => 'array',
        'config' => 'array',
    ];

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'difficulty',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'difficulty' => 'integer',
    ];

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }
}

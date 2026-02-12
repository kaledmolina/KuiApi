<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteAudio extends Model
{
    use HasFactory;

    protected $table = 'note_audios';

    protected $fillable = [
        'note_name',
        'octave',
        'full_name',
        'file_path',
    ];
}

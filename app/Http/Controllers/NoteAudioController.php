<?php

namespace App\Http\Controllers;

use App\Models\NoteAudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoteAudioController extends Controller
{
    public function index(Request $request)
    {
        $query = NoteAudio::query();

        if ($request->has('octave')) {
            $query->where('octave', $request->octave);
        }

        if ($request->has('note_name')) {
            $notes = $request->input('note_name');
            if (is_string($notes) && str_contains($notes, ',')) {
                $notes = explode(',', $notes);
            }
            if (is_array($notes)) {
                $query->whereIn('note_name', $notes);
            } else {
                $query->where('note_name', $notes);
            }
        }

        return response()->json($query->get());
    }

    public function generator()
    {
        return view('audio_generator');
    }

    public function store(Request $request)
    {
        $request->validate([
            'note_name' => 'required',
            'octave' => 'required',
            'audio' => 'required|file|mimes:mp3,wav,webm,audio/webm',
        ]);

        $full_name = $request->note_name . $request->octave;
        $path = $request->file('audio')->storeAs('notes', $full_name . '.webm', 'public');

        NoteAudio::updateOrCreate(
            ['full_name' => $full_name],
            [
                'note_name' => $request->note_name,
                'octave' => $request->octave,
                'file_path' => Storage::url($path),
            ]
        );

        return response()->json(['success' => true, 'path' => Storage::url($path)]);
    }
}

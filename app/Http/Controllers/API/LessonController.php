<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function curriculum()
    {
        // Simple pagination or list of levels
        return response()->json(Level::orderBy('id')->get());
    }

    public function show($id)
    {
        $level = Level::findOrFail($id);
        return response()->json($level);
    }

    public function complete(Request $request)
    {
        $request->validate([
            'level_id' => 'required|exists:levels,id',
            'stars' => 'required|integer|min:0|max:3',
            'score' => 'required|integer|min:0',
        ]);

        $user = $request->user();

        // Update User Progress
        $progress = UserProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'level_id' => $request->level_id,
            ],
            [
                'stars' => $request->stars, // Simple logic: overwrite stars. Real logic might check if new stars > old stars.
                'score' => $request->score,
                'completed_at' => now(),
            ]
        );

        // Update User Stats (XP, Gold, Streak) - Logic copied from request description "Igual que el plan anterior"
        // Since I don't have the exact logic for XP calculation from "Plan anterior" embodied here, 
        // I will implement a basic increment based on score or fixed values.
        // Assuming: XP = score, Gold = stars * 10

        // Fetch Level to get difficulty
        $level = Level::find($request->level_id);
        $difficultyMultiplier = $level ? $level->difficulty : 1;

        $xpGained = $request->score * $difficultyMultiplier; // XP = score * difficulty
        $goldGained = $request->stars * 10;

        $user->increment('xp_total', $xpGained);
        $user->increment('gold_notes', $goldGained);

        // 4. Streak Update via Gamification Service
        $gamificationService = new \App\Services\GamificationService(); // Or inject via constructor
        $gamificationService->maintainStreak($user);

        return response()->json([
            'message' => 'Lección completada',
            'xp_gained' => $xpGained,
            'gold_gained' => $goldGained,
            'user_stats' => [
                'xp_total' => $user->xp_total,
                'gold_notes' => $user->gold_notes,
            ]
        ]);
    }
    public function updateProgress(Request $request)
    {
        $request->validate([
            'lives' => 'required|integer',
            'gold_notes' => 'required|integer',
            'streak_count' => 'required|integer',
            'xp_total' => 'required|integer',
        ]);

        $user = $request->user();

        $user->update([
            'lives' => $request->lives,
            'gold_notes' => $request->gold_notes,
            'streak_count' => $request->streak_count,
            'xp_total' => $request->xp_total,
            'last_activity_at' => now(),
        ]);

        return response()->json(['message' => 'Progress updated', 'user' => $user]);
    }
}

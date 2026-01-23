<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function updateStats(Request $request)
    {
        $request->validate([
            'lives' => 'nullable|integer|min:0|max:5',
            'streak_count' => 'nullable|integer|min:0',
            'xp' => 'nullable|integer|min:0',
            'gold_notes' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();

        if ($request->has('lives')) {
            $user->lives = $request->lives;
        }

        // Daily Streak Logic (Server-Side)
        $now = now();
        $lastActivity = $user->last_activity_at;

        if ($lastActivity) {
            // Check if last activity was yesterday (continued streak)
            // referencing Carbon instance
            if ($lastActivity->isYesterday()) {
                $user->streak_count++;
            }
            // Check if last activity was BEFORE yesterday (broken streak)
            // If it's not today and not yesterday, it must be older.
            elseif (!$lastActivity->isToday()) {
                $user->streak_count = 1;
            }
            // If isToday(), streak remains same.
        } else {
            // First time activity
            $user->streak_count = 1;
        }

        $user->last_activity_at = $now;

        if ($request->has('xp')) {
            // XP usually accumulates, but if we pass total, update total
            // Or if we pass increment, logic should handle it. 
            // For simplicity, let's assume we pass the *new total* or *increment*?
            // User model has 'xp_total'. Let's assume we pass the delta to add.
            // Wait, front-end might send total. Let's assume 'xp' param is added to total.
            $user->xp_total += $request->xp;
        }

        if ($request->has('gold_notes')) {
            $user->gold_notes += $request->gold_notes;
        }

        if ($request->has('max_unlocked_level')) {
            if ($request->max_unlocked_level > $user->max_unlocked_level) {
                $user->max_unlocked_level = $request->max_unlocked_level;
            }
        }

        $user->save();

        return response()->json([
            'message' => 'Stats updated successfully',
            'user' => $user
        ]);
    }
}

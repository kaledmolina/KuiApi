<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TrainingController extends Controller
{
    public function complete(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:10',
        ]);

        $user = $request->user();
        $score = $request->score;
        $now = now();

        // --- 1. XP REWARDS (Unlimited) ---
        // 10 XP per point
        $earnedXp = $score * 10;
        $user->xp_total += $earnedXp;

        // --- 2. LIVES REWARDS (Capped daily) ---
        $earnedLives = 0;

        // Reset daily counter if it's a new day
        if ($user->last_farmed_at) {
            $lastFarmed = Carbon::parse($user->last_farmed_at);
            if (!$lastFarmed->isSameDay($now)) {
                $user->lives_farmed_daily = 0;
            }
        }

        // Logic: 
        // Score 0-3: 1 Life
        // Score 4-7: 2 Lives
        // Score 8-10: 3 Lives
        // Capped by remaining daily limit (Max 3 total/day)

        $potentialLives = 0;
        if ($score >= 8)
            $potentialLives = 3;
        else if ($score >= 4)
            $potentialLives = 2;
        else
            $potentialLives = 1;

        $dailyLimit = 3;
        $livesAlreadyFarmed = $user->lives_farmed_daily;
        $remainingLimit = max(0, $dailyLimit - $livesAlreadyFarmed);

        // Actual lives granted is min(potential, remaining)
        $livesToGrant = min($potentialLives, $remainingLimit);

        if ($livesToGrant > 0) {
            $currentLives = $user->lives;
            // Cap total lives at 5 (game rule)
            $newLives = min(5, $currentLives + $livesToGrant);

            // Only increment farmed count if we actually added lives (or should we count attempts? let's count granted)
            // Actually, if user is already at 5 lives, they don't "gain" a life in inventory, but did they "farm" it?
            // Let's say yes, they consumed their daily chance. 
            // Better UX: If they are full, tell them they are full. 
            // But for simplicity: Just add what we can.

            $livesAdded = $newLives - $currentLives;

            // Wait, if I have 5 lives, I shouldn't be farming training for lives, just XP.
            // Let's just track granted lives.

            $user->lives = $newLives;
            $user->lives_farmed_daily += $livesToGrant;
            // Note: If I had 4 lives, and earned 2. I go to 5. livesAdded=1. 
            // Should I count 2 against daily limit? Yes. The effort was for 2.
            // Simplified: User gets credit for what the server decides to give.

            $earnedLives = $livesToGrant; // This is what we tell the user they "won"
        }

        $user->last_farmed_at = $now;
        $user->save();

        return response()->json([
            'message' => 'Training session completed',
            'earned_xp' => $earnedXp,
            'earned_lives' => $earnedLives,
            'lives_farmed_today' => $user->lives_farmed_daily,
            'daily_limit' => $dailyLimit,
            'user_stats' => [ // Return full updated stats for smart sync
                'lives' => $user->lives,
                'xp_total' => $user->xp_total,
                'streak_count' => $user->streak_count,
            ]
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class GamificationService
{
    /**
     * Check daily login status: Regenerate lives and update streak if needed.
     */
    public function handleDailyLogin(User $user): void
    {
        $now = now();
        $lastActivity = $user->last_activity_at ? Carbon::parse($user->last_activity_at) : null;
        $lastLifeRegen = $user->last_life_regenerated_at ? Carbon::parse($user->last_life_regenerated_at) : null;

        // 1. Daily Lives Reset (5 lives per day)
        if (!$lastLifeRegen || !$lastLifeRegen->isSameDay($now)) {
            $user->lives = 5;
            $user->last_life_regenerated_at = $now;
        }

        // 2. Streak Logic (Login based)
        // Note: Some apps update streak only on lesson completion. 
        // We kept the logic from AuthController here for consistency if "login" counts as activity.
        if ($lastActivity) {
            if ($lastActivity->isYesterday()) {
                // Continued streak
                $user->increment('streak_count');
            } elseif (!$lastActivity->isToday()) {
                // Broken streak (older than yesterday)
                $user->streak_count = 1;
            }
            // If isToday, do nothing
        } else {
            // First time
            $user->streak_count = 1;
        }

        $user->last_activity_at = $now;
        $user->save();
    }

    /**
     * Deduct a life from the user.
     * Returns true if life deducted, false if no lives left.
     */
    public function deductLife(User $user): bool
    {
        if ($user->lives > 0) {
            $user->decrement('lives');
            return true;
        }
        return false;
    }

    /**
     * Increment streak (e.g. after lesson completion).
     * This logic mimics the daily login check but specifically for "Lesson Complete" events.
     */
    public function maintainStreak(User $user): void
    {
        $now = now();
        $lastActivity = $user->last_activity_at ? Carbon::parse($user->last_activity_at) : null;

        if ($lastActivity) {
            if ($lastActivity->isYesterday()) {
                $user->increment('streak_count');
            } elseif (!$lastActivity->isToday()) {
                $user->streak_count = 1;
            }
        } else {
            $user->streak_count = 1;
        }

        $user->last_activity_at = $now;
        $user->save();
    }

    /**
     * Handle practice completion.
     * Award Life + XP if under daily limit (3).
     * Award only XP if over limit.
     */
    public function completePractice(User $user): array
    {
        $now = now();
        $lastFarmed = $user->last_farmed_at ? Carbon::parse($user->last_farmed_at) : null;

        // Reset daily counter if new day
        if (!$lastFarmed || !$lastFarmed->isSameDay($now)) {
            $user->lives_farmed_daily = 0;
        }

        $gainedLife = false;
        $xpGained = 0;
        $message = '';

        if ($user->lives_farmed_daily < 3) {
            // Farm Life
            if ($user->lives < 5) { // Assuming 5 is max lives
                $user->increment('lives');
                $gainedLife = true;
            }
            $user->increment('lives_farmed_daily');

            // Award Practice XP (e.g. 10 XP)
            $xpGained = 10;
            $message = 'Practice Complete! +1 Heart, +10 XP';
        } else {
            // Daily limit reached, only XP (reduced?)
            $xpGained = 5;
            $message = 'Daily hearts limit reached. +5 XP';
        }

        $user->increment('xp_total', $xpGained);
        $user->last_farmed_at = $now;
        $user->last_activity_at = $now; // Also counts as activity
        $user->save();

        return [
            'gained_life' => $gainedLife,
            'xp_gained' => $xpGained,
            'lives' => $user->lives,
            'lives_farmed_today' => $user->lives_farmed_daily,
            'message' => $message
        ];
    }
}

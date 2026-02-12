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
}

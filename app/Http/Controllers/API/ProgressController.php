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

        if ($request->has('streak_count')) {
            $user->streak_count = $request->streak_count;
        }

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

        $user->save();

        return response()->json([
            'message' => 'Stats updated successfully',
            'user' => $user
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $league = $request->query('league');

            // Optionally, trigger a check for the authenticated user, but we'll do that in XP awards mostly.
            // If we want the ranking to always be perfectly fresh, we might need a batch update or trust the login/XP hook.
            // For now, let's just query.

            $query = User::select('id', 'name', 'xp_total', 'xp_monthly', 'league', 'max_unlocked_level')
                ->where('is_active', true)
                ->orderBy('xp_monthly', 'desc');

            if ($league) {
                $query->where('league', $league);
            }

            $rankings = $query->limit(50)->get();

            return response()->json($rankings);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error in RankingController',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}

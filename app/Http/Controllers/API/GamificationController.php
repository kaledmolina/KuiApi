<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function deductLife(Request $request)
    {
        $user = $request->user();

        $deducted = $this->gamificationService->deductLife($user);

        if ($deducted) {
            return response()->json([
                'message' => 'Life deducted',
                'lives' => $user->lives
            ]);
        } else {
            return response()->json([
                'message' => 'No lives left',
                'lives' => 0
            ], 400); // Or 402 Payment Required? 400 is fine for now.
        }
    }
}

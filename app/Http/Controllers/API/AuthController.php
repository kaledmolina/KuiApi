<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Tu cuenta ha sido suspendida. Contacta soporte.',
            ], 403);
        }

        // Gamification Logic: Daily Reset & Streak
        $now = now();
        $lastActivity = $user->last_activity_at ? \Carbon\Carbon::parse($user->last_activity_at) : null;
        $lastLifeRegen = $user->last_life_regenerated_at ? \Carbon\Carbon::parse($user->last_life_regenerated_at) : null;

        // 1. Daily Lives Reset (5 lives per day)
        if (!$lastLifeRegen || !$lastLifeRegen->isSameDay($now)) {
            $user->lives = 5;
            $user->last_life_regenerated_at = $now;
        }

        // 2. Streak Logic
        if ($lastActivity) {
            if ($lastActivity->isYesterday()) {
                // Continued streak
                $user->increment('streak_count');
            } elseif (!$lastActivity->isToday()) {
                // Broken streak (older than yesterday)
                $user->streak_count = 1;
            }
            // If isToday, do nothing (streak already counted for today)
        } else {
            // First time login
            $user->streak_count = 1;
        }

        $user->last_activity_at = $now;
        $user->save();


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}

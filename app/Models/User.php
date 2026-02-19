<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'xp_total',
        'xp_monthly',
        'lives',
        'last_life_regenerated_at',
        'streak_count',
        'last_activity_at',
        'gold_notes',
        'max_unlocked_level',
        'league',
        'last_league_reset_at',
        'is_admin',
        'lives_farmed_daily',
        'last_farmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'last_life_regenerated_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'last_league_reset_at' => 'datetime',
        ];
    }

    public function checkAndResetMonthlyLeague(): void
    {
        $now = now();
        $resetNeeded = false;

        if (!$this->last_league_reset_at) {
            $resetNeeded = true;
        } else {
            // If the month or year is different, we need a reset
            if ($this->last_league_reset_at->format('Y-m') !== $now->format('Y-m')) {
                $resetNeeded = true;
            }
        }

        if ($resetNeeded) {
            $this->xp_monthly = 0;
            $this->last_league_reset_at = $now;
            // The actual promotion/demotion logic could be here globally or handled per user.
            // For now, we just reset their monthly XP to 0 so they climb again.
            $this->save();
        }
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }
}

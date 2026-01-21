<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->integer('xp_total')->default(0);
            $table->integer('lives')->default(5);
            $table->timestamp('last_life_regenerated_at')->nullable();
            $table->integer('streak_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->integer('gold_notes')->default(0);
            $table->boolean('is_admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'xp_total',
                'lives',
                'last_life_regenerated_at',
                'streak_count',
                'last_activity_at',
                'gold_notes',
                'is_admin',
            ]);
        });
    }
};

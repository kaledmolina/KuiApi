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
        Schema::create('note_audios', function (Blueprint $table) {
            $table->id();
            $table->string('note_name'); // e.g., C, C#, D
            $table->string('octave');    // e.g., 3, 4
            $table->string('full_name'); // e.g., C3, C#4
            $table->string('file_path'); // Path to the file in storage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_audios');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_career_paths', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('career_path_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('in_progress'); // in_progress|completed|paused
            $table->unsignedTinyInteger('progress')->default(0); // 0-100
            $table->timestamps();
            $table->primary(['user_id', 'career_path_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_career_paths');
    }
};

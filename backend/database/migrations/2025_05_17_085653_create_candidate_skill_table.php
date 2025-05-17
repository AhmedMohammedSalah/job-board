<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidate_skills', function (Blueprint $table) {
             $table->id();

    $table->unsignedBigInteger('candidate_id');
    $table->unsignedBigInteger('skill_id');

    $table->enum('level', ['beginner', 'intermediate', 'expert'])->default('intermediate');
    $table->timestamps();

    // Explicit foreign key names
     $table->foreign('candidate_id', 'fk_candidate_skill_candidates')
          ->references('id')
          ->on('candidates')
          ->cascadeOnDelete();

    $table->foreign('skill_id', 'fk_candidate_skill_skills')
          ->references('id')
          ->on('skills')
          ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_skills');
    }
};

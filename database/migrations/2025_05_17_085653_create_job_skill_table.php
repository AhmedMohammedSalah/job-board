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
        Schema::create('job_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->enum('level', ['beginner', 'intermediate', 'expert'])->default('intermediate');
            $table->timestamps();

            // Explicit foreign key names
            $table->foreign('job_id', 'fk_job_skill_job')
                ->references('id')
                ->on('jobs');

            $table->foreign('skill_id', 'fk_job_skill_skill')
                ->references('id')
                ->on('skills');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_skill');
    }
};

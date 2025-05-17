<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\ApplicationStatus;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('candidate_id');
            $table->string('resume_path');
            $table->text('cover_letter')->nullable();
            $table->enum('status', [
                ApplicationStatus::PENDING->value,
                ApplicationStatus::REVIEWED->value,
                ApplicationStatus::ACCEPTED->value,
                ApplicationStatus::REJECTED->value
            ])->default(ApplicationStatus::PENDING->value);
            $table->timestamps();
            $table->foreign('job_id', 'fk_applications_jobs')
                ->references('id')
                ->on('jobs')
                ->cascadeOnDelete();

            $table->foreign('candidate_id', 'fk_applications_candidates')
                ->references('id')
                ->on('candidates')
                ->cascadeOnDelete();
            // Prevent duplicate applications
            $table->unique(['job_id', 'candidate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

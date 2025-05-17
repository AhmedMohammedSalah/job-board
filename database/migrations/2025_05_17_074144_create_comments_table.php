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
        Schema::create('comments', function (Blueprint $table) {
           $table->id();

    // Define columns first
    $table->unsignedBigInteger('job_id');
    $table->unsignedBigInteger('user_id');
    $table->text('content');
    $table->timestamps();

    // Add foreign keys with explicit names
    $table->foreign('job_id', 'fk_comments_jobs')
          ->references('id')
          ->on('jobs')
          ->onDelete('cascade')
          ->onUpdate('cascade');

    $table->foreign('user_id', 'fk_comments_users')
          ->references('id')
          ->on('users')
          ->onDelete('cascade')
          ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

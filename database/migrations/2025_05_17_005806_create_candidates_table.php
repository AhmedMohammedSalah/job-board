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
        Schema::create('candidates', function (Blueprint $table) {
             // Same ID as users table
            $table->unsignedBigInteger('id')->primary();
                // Profile fields
            $table->string('headline')->nullable();
            $table->text('skills')->nullable();
            $table->string('resume_path')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('linkedin_url')->nullable();
            $table->timestamps();
            // Foreign key
            $table-> foreign('id')->references('id')->on('users')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

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
        Schema::create('jobs', function (Blueprint $table) {
                $table->id();

                // Add employer_id before creating foreign key
                $table->unsignedBigInteger('employer_id');
                $table->unsignedBigInteger('category_id');

                // Explicitly name foreign keys
                $table->foreign('employer_id', 'fk_jobs_employers')
                    ->references('id')
                    ->on('employers')
                    ->cascadeOnDelete();

                $table->foreign('category_id', 'fk_jobs_categories')
                    ->references('id')
                    ->on('categories')
                    ->cascadeOnDelete();

                // Other columns
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description');
                $table->text('responsibilities');
                $table->text('requirements');
                $table->text('benefits')->nullable();
                $table->enum('work_type', ['remote', 'onsite', 'hybrid']);
                $table->string('location');
                $table->decimal('min_salary', 10, 2)->nullable();
                $table->decimal('max_salary', 10, 2)->nullable();
                $table->date('deadline');
                $table->enum('status', ['draft', 'pending', 'published', 'expired'])->default('draft');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

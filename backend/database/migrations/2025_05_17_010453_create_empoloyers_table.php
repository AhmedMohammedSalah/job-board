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
        Schema::create('employers', function (Blueprint $table) {
            // Explicitly name the foreign key constraint
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id', 'fk_employers_users_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            // Other columns
            $table->string('company_name');
            $table->text('company_description')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};

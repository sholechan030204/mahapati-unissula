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
        Schema::create('registration_applicants', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name', 180);
            $table->string('student_number', 40)->index();
            $table->string('study_program', 150);
            $table->string('cohort', 12);
            $table->unsignedTinyInteger('semester');
            $table->string('email', 190)->index();
            $table->string('whatsapp', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_applicants');
    }
};

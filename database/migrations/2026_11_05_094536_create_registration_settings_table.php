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
        Schema::create('registration_settings', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_open')->default(false);
            $table->date('opens_at')->nullable();
            $table->date('closes_at')->nullable();
            $table->date('interview_starts_at')->nullable();
            $table->date('interview_ends_at')->nullable();
            $table->date('announcement_at')->nullable();
            $table->unsignedInteger('quota')->nullable();
            $table->unsignedBigInteger('fee')->nullable();
            $table->string('whatsapp_group_link', 2048)->nullable();
            $table->string('pamphlet_path')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_settings');
    }
};

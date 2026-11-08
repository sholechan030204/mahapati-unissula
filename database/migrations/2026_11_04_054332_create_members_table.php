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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('alam_name')->nullable();
            $table->string('nra', 50)->unique();
            $table->string('nim', 30)->nullable()->index();
            $table->string('membership_type', 30)->default('anggota_tetap')->index();
            $table->string('status', 20)->default('aktif')->index();
            $table->string('position')->nullable();
            $table->string('profile_photo');
            $table->text('address');
            $table->string('phone', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

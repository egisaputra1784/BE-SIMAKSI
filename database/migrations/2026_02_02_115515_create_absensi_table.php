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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sesi_absen_id')
                ->constrained('sesi_absen')
                ->cascadeOnDelete();

            $table->foreignId('murid_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', ['hadir', 'izin', 'alpha']);
            $table->timestamp('waktu_scan');

            $table->timestamp('created_at')->useCurrent();

            $table->unique(['sesi_absen_id', 'murid_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};

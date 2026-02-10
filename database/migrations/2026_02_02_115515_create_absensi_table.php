<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiTable extends Migration
{
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_absen_id')->constrained('sesi_absen');
            $table->foreignId('murid_id')->constrained('users');
            $table->enum('status', ['hadir','izin','sakit','alpha']);
            $table->timestamp('waktu_scan');
            $table->decimal('latitude',10,7)->nullable();
            $table->decimal('longitude',10,7)->nullable();
            $table->timestamps();

            $table->unique(['sesi_absen_id','murid_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi');
    }
}

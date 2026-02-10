<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSesiAbsenTable extends Migration
{
    public function up()
    {
        Schema::create('sesi_absen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal'); 
            $table->date('tanggal');
            $table->string('token_qr');
            $table->timestamp('expired_at');
            $table->enum('tipe', ['masuk','mapel','pulang']);
            $table->foreignId('dibuka_oleh')->constrained('users');
            $table->timestamp('dibuka_pada');
            $table->timestamp('ditutup_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sesi_absen');
    }
}

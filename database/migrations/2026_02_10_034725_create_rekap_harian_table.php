<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekapHarianTable extends Migration
{
    public function up()
    {
        Schema::create('rekap_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('users');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->date('tanggal');
            $table->integer('total_mapel');
            $table->integer('hadir_mapel');
            $table->float('persentase');
            $table->enum('status', ['hadir','izin','sakit','alpha']);
            $table->timestamp('jam_masuk')->nullable();
            $table->timestamp('jam_pulang')->nullable();
            $table->timestamps();

            $table->unique(['murid_id','tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekap_harian');
    }
}

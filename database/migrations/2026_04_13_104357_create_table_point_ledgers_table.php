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
        Schema::create('point_ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('transaction_type', ['EARN', 'SPEND', 'PENALTY']);
            $table->integer('amount');
            $table->integer('current_balance');

            $table->text('description')->nullable();

            $table->foreignId('absensi_id')
                ->nullable()
                ->constrained('absensi')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_ledgers');
    }
};

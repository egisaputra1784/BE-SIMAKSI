<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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

            // 🔥 EVENT TRACKING
            $table->enum('event_type', ['ATTENDANCE', 'BUY_ITEM', 'VOUCHER_USED'])
                ->nullable();

            // 🔥 RELASI TAMBAHAN
            $table->foreignId('item_id')
                ->nullable()
                ->constrained('flexibility_items')
                ->nullOnDelete();

            $table->foreignId('used_token_id')
                ->nullable()
                ->constrained('user_tokens')
                ->nullOnDelete();

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

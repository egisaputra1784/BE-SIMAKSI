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
        Schema::table('flexibility_items', function (Blueprint $table) {
            $table->enum('type', ['LATE', 'ALPHA'])->after('point_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flexibility_items', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });
    }
};

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
        Schema::table('flexibility_items', function (Blueprint $table) {
            $table->integer('max_late_minutes')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('flexibility_items', function (Blueprint $table) {
            $table->dropColumn(['max_late_minutes']);
        });
    }
};

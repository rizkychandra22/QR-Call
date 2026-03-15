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
        Schema::table('presents', function (Blueprint $table) {
            $table->unique(['user_id', 'qr_code_id'], 'presents_user_qr_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presents', function (Blueprint $table) {
            $table->dropUnique('presents_user_qr_unique');
        });
    }
};

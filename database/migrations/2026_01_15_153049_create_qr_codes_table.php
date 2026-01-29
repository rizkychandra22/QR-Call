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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->string('qr_code_present')->unique();                      // Token/UUID unik untuk QR Code
            $table->enum('present', ['in_present', 'out_present']);   // QR untuk absen masuk / keluar
            $table->date('date');                                     // Tanggal absen
            $table->datetime('start_time');                           // Waktu mulai QR active
            $table->datetime('end_time');                             // Waktu QR berakhir / expired
            $table->enum('status', ['active', 'expired'])->default('active'); // Status QR
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};

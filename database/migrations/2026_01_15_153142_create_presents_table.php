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
        Schema::create('presents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('qr_code_id')->constrained()->cascadeOnDelete()->unique();
            $table->date('date');
            $table->time('time');
            $table->string('hours')->nullable();                     // Total jam kerja
            $table->string('present_desc_system')->nullable();       // Keterangan otomatis dari sistem
            $table->string('present_user_desc')->nullable();         // Deskripsi tambahan dari user
            $table->string('present_user_image')->nullable();        // Foto dokumentasi
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alfa']);
            $table->string('status_desc')->nullable();               // Keterangan izin/sakit
            $table->string('status_image')->nullable();              // File bukti izin/sakit
            $table->double('lat_location_present')->nullable();
            $table->double('lng_location_present')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presents');
    }
};

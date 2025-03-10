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
        Schema::create('xep_hang_sang_kien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dot_danh_gia_id')->constrained('dot_danh_gia');
            $table->string('ten_xep_hang');
            $table->string('mo_ta');
            $table->integer('khung_diem_min');
            $table->integer('khung_diem_max');
            $table->integer('giai_thuong');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xep_hang_sang_kien');
    }
};

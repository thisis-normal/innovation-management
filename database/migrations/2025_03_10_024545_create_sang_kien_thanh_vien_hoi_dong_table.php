<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sang_kien_thanh_vien_hoi_dong', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ma_sang_kien');
            $table->unsignedBigInteger('ma_thanh_vien');
            $table->boolean('da_duyet')->nullable()->default(null);
            $table->timestamps();

            // Foreign keys
            $table->foreign('ma_sang_kien')->references('id')->on('sang_kien')->onDelete('cascade');
            $table->foreign('ma_thanh_vien')->references('id')->on('thanh_vien_hoi_dong')->onDelete('cascade');

            // Unique constraint để tránh trùng lặp
            $table->unique(['ma_sang_kien', 'ma_thanh_vien']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sang_kien_thanh_vien_hoi_dong');
    }
};

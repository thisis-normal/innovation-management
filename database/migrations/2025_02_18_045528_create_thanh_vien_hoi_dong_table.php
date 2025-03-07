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
        Schema::create('thanh_vien_hoi_dong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ma_hoi_dong')->comment('Mã hội đồng')
                ->constrained('hoi_dong_tham_dinh')
                ->onDelete('cascade');
            $table->foreignId('ma_nguoi_dung')->comment('Mã người dùng trong hội đồng')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['ma_hoi_dong', 'ma_nguoi_dung'], 'unique_member_council');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_vien_hoi_dong');
    }
};

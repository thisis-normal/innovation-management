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
        Schema::create('diem_hoi_dong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ma_sang_kien')
                ->constrained('sang_kien')
                ->onDelete('cascade');
            $table->foreignId('ma_hoi_dong')
                ->constrained('hoi_dong_tham_dinh')
                ->onDelete('cascade');
            $table->integer('diem_cuoi')->comment('Điểm tổng của sáng kiến, được đánh giá bởi cả nhóm hội đồng');
            $table->text('nhan_xet_chung')->nullable();
            $table->foreignId('nguoi_nhap')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['ma_sang_kien', 'ma_hoi_dong']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diem_hoi_dong');
    }
};

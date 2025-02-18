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
        Schema::create('hoi_dong_tham_dinh', function (Blueprint $table) {
            $table->id();
            $table->string('ten_hoi_dong');
            $table->foreignId('ma_truong_hoi_dong')->comment('Trưởng hội đồng')
                ->constrained('users')
                ->onDelete('cascade');
            $table->date('ngay_bat_dau')->default(now());
            $table->date('ngay_ket_thuc')->nullable();
            $table->tinyInteger('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoi_dong_tham_dinh');
    }
};

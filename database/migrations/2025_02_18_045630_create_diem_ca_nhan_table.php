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
        Schema::create('diem_ca_nhan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ma_sang_kien')
                ->constrained('sang_kien')
                ->onDelete('cascade');
            $table->foreignId('ma_thanh_vien')->comment('Mã thành viên trong hội đồng')
                ->constrained('thanh_vien_hoi_dong')
                ->onDelete('cascade');
            $table->integer('diem');
            $table->text('nhan_xet')->nullable();
            $table->timestamps();
            $table->unique(['ma_sang_kien', 'ma_thanh_vien']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diem_ca_nhan');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSangKienThanhVienHoiDongTable extends Migration
{
    public function up()
    {
        Schema::create('sang_kien_thanh_vien_hoi_dong', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ma_sang_kien'); // Khóa ngoại liên kết với bảng sang_kien
            $table->unsignedBigInteger('ma_thanh_vien'); // Khóa ngoại liên kết với bảng thanh_vien_hoi_dong
            $table->boolean('da_duyet')->nullable(); // Cột để ghi nhận trạng thái phê duyệt (null: chưa duyệt, 0: từ chối, 1: phê duyệt)
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('ma_sang_kien')->references('id')->on('sang_kien')->onDelete('cascade');
            $table->foreign('ma_thanh_vien')->references('id')->on('thanh_vien_hoi_dong')->onDelete('cascade');

            // Khóa chính kết hợp (nếu cần)
            $table->unique(['ma_sang_kien', 'ma_thanh_vien']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sang_kien_thanh_vien_hoi_dong');
    }
}

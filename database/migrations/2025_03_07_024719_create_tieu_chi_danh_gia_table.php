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
        Schema::create('tieu_chi_danh_gia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dot_danh_gia_id');
            $table->foreign('dot_danh_gia_id')->references('id')->on('dot_danh_gia')->onDelete('cascade');
            $table->string('ten_tieu_chi', 255);
            $table->text('mo_ta')->nullable();
            $table->integer('diem_toi_da')->default(0);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tieu_chi_danh_gia');
    }
};

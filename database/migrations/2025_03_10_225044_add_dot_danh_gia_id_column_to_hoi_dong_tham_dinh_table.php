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
        Schema::table('hoi_dong_tham_dinh', function (Blueprint $table) {
            $table->unsignedBigInteger('dot_danh_gia_id')->nullable();
            $table->foreign('dot_danh_gia_id')->references('id')->on('dot_danh_gia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoi_dong_tham_dinh', function (Blueprint $table) {
            $table->dropForeign(['dot_danh_gia_id']);
            $table->dropColumn('dot_danh_gia_id');
        });
    }
};

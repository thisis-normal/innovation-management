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
        Schema::table('dot_danh_gia', function (Blueprint $table) {
            $table->date('ngay_bat_dau')->nullable()->after('mo_ta');
            $table->date('ngay_ket_thuc')->nullable()->after('ngay_bat_dau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dot_danh_gia', function (Blueprint $table) {
            $table->dropColumn('ngay_bat_dau');
            $table->dropColumn('ngay_ket_thuc');
        });
    }
};

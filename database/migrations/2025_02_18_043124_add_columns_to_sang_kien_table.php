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
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->text('hien_trang')->nullable();
            $table->text('ket_qua')->nullable();
            $table->text('ghi_chu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->dropColumn('hien_trang');
            $table->dropColumn('ket_qua');
            $table->dropColumn('ghi_chu');
        });
    }
};

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
        if (!Schema::hasColumn('sang_kien', 'ma_hoi_dong')) {
            Schema::table('sang_kien', function (Blueprint $table) {
                $table->unsignedBigInteger('ma_hoi_dong')->nullable()->after('ma_trang_thai_sang_kien');

                $table->foreign('ma_hoi_dong')
                    ->references('id')
                    ->on('hoi_dong_tham_dinh')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ma_hoi_dong');
        });
    }
};

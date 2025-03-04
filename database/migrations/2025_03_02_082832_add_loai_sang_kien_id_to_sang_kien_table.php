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
            // Add the column first
            $table->unsignedBigInteger('ma_loai_sang_kien')->nullable()->after('ma_don_vi');
            // Then add the foreign key constraint
            $table->foreign('ma_loai_sang_kien')
                ->references('id')
                ->on('loai_sang_kien')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->dropForeign(['ma_loai_sang_kien']);
            $table->dropColumn('ma_loai_sang_kien');
        });
    }
};

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
        Schema::table('thanh_vien_hoi_dong', function (Blueprint $table) {
            $table->dropColumn('da_duyet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thanh_vien_hoi_dong', function (Blueprint $table) {
            $table->boolean('da_duyet')->default(false);
        });
    }
};

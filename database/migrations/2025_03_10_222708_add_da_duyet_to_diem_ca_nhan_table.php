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
        Schema::table('diem_ca_nhan', function (Blueprint $table) {
            $table->boolean('da_duyet')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diem_ca_nhan', function (Blueprint $table) {
            $table->dropColumn('da_duyet');
        });
    }
};

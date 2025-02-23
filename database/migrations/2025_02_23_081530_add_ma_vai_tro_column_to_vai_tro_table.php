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
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->string('ma_vai_tro')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->dropColumn('ma_vai_tro');
        });
    }
};

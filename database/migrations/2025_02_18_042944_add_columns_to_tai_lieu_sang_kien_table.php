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
        Schema::table('tai_lieu_sang_kien', function (Blueprint $table) {
            $table->unsignedBigInteger('sang_kien_id')->nullable();
            $table->foreign('sang_kien_id')->references('id')->on('sang_kien')->onDelete('cascade');
            $table->string('file_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tai_lieu_sang_kien', function (Blueprint $table) {
            $table->dropForeign(['sang_kien_id']);
            $table->dropColumn('sang_kien_id');
            $table->dropColumn('file_path');
        });
    }
};

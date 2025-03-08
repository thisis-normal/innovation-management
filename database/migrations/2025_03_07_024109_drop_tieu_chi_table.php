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
        Schema::dropIfExists('tieu_chi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tieu_chi', function (Blueprint $table) {
            $table->id();
            $table->string('stt')->nullable();
            $table->string('ten_tieu_chi')->nullable();
            $table->string('ghi_chu')->nullable();
            $table->timestamps();
        });
    }
};

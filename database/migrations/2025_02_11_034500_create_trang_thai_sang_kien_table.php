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
        Schema::create('trang_thai_sang_kien', function (Blueprint $table) {
            $table->id();
            $table->string('ten_trang_thai');
            $table->string('mo_ta')->nullable();
            $table->integer('order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trang_thai_sang_kien');
    }
};

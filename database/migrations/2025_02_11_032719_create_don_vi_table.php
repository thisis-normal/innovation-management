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
        Schema::create('don_vi', function (Blueprint $table) {
            $table->id();
            $table->string('ten_don_vi');
            $table->string('mo_ta')->nullable();
            $table->foreignId('don_vi_cha_id')->nullable()->constrained('don_vi');
            $table->tinyInteger('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_vi');
    }
};

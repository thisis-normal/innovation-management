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
        Schema::create('vai_tro', function (Blueprint $table) {
            $table->id();
            $table->string('ten_vai_tro');
            $table->string('mo_ta')->nullable();
            $table->tinyInteger('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vai_tro');
    }
};

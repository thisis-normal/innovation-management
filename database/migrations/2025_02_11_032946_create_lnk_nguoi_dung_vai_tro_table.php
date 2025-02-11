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
        Schema::create('lnk_nguoi_dung_vai_tro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vai_tro_id')->constrained('vai_tro')->cascadeOnDelete();
            $table->unique(['nguoi_dung_id', 'vai_tro_id']);
            $table->foreignId('nguoi_tao')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nguoi_cap_nhat')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lnk_nguoi_dung_vai_tro');
    }
};

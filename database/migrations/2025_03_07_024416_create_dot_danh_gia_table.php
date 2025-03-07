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
        Schema::create('dot_danh_gia', function (Blueprint $table) {
            $table->id();
            $table->year('nam')->comment('Năm đánh giá');
            $table->integer('so_dot')->comment('Số đợt đánh giá');
            $table->string('mo_ta', 255)->nullable()->default(null);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->unique(['nam', 'so_dot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dot_danh_gia');
    }
};

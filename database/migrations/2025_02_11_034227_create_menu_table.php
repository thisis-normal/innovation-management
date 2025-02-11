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
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('icon');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('menu')->onDelete('cascade');
            $table->integer('order');
            $table->boolean('is_active')->default(true);
            //them vai tro id link toi bang vai tro
            $table->unsignedBigInteger('vai_tro_id');
            $table->foreign('vai_tro_id')->references('id')->on('vai_tro')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};

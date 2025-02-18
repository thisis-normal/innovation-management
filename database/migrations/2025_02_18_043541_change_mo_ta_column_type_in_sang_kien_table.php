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
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->text('mo_ta')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->string('mo_ta')->nullable()->change();
        });
    }
};

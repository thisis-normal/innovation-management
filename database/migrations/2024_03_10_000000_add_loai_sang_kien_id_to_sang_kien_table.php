<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->foreignId('loai_sang_kien_id')
                ->nullable()
                ->constrained('loai_sang_kien')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->dropForeign(['loai_sang_kien_id']);
            $table->dropColumn('loai_sang_kien_id');
        });
    }
};

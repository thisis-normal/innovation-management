<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hoi_dong_tham_dinh', function (Blueprint $table) {
            $table->foreignId('don_vi_id')->nullable()->constrained('don_vi')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('hoi_dong_tham_dinh', function (Blueprint $table) {
            $table->dropForeign(['don_vi_id']);
            $table->dropColumn('don_vi_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('thanh_vien_hoi_dong', function (Blueprint $table) {
            $table->boolean('da_duyet')->default(false);
        });
    }

    public function down()
    {
        Schema::table('thanh_vien_hoi_dong', function (Blueprint $table) {
            $table->dropColumn('da_duyet');
        });
    }
};

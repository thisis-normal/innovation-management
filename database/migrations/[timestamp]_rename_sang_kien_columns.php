<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->renameColumn('hien_trang', 'truoc_khi_ap_dung');
            $table->renameColumn('ket_qua', 'sau_khi_ap_dung');
        });
    }

    public function down()
    {
        Schema::table('sang_kien', function (Blueprint $table) {
            $table->renameColumn('truoc_khi_ap_dung', 'hien_trang');
            $table->renameColumn('sau_khi_ap_dung', 'ket_qua');
        });
    }
};

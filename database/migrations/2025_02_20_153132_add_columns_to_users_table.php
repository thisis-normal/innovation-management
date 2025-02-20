<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(); // First, make it nullable
        });
        // Update existing records: Set username as the prefix of email
        DB::statement("UPDATE users SET username = SUBSTRING_INDEX(email, '@', 1) WHERE username IS NULL");
        // Now, make it NOT NULL
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
        });
        //add ma_don_vi column
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('ma_don_vi')->nullable();
            $table->foreign('ma_don_vi')->references('id')->on('don_vi')->onDelete('cascade');
        });
        //add ma_nhan_vien column
        Schema::table('users', function (Blueprint $table) {
            $table->string('ma_nhan_vien')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('ma_don_vi');
            $table->dropColumn('ma_nhan_vien');
        });
    }
};

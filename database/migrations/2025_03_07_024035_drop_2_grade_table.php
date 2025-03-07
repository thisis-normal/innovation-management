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
        //drop grade_ranges & grading_sessions
        Schema::dropIfExists('grade_ranges');
        Schema::dropIfExists('grading_sessions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Do nth
    }
};

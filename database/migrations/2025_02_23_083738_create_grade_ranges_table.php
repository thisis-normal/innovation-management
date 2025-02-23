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
        Schema::create('grade_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_session_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('grade_name', 50);
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_ranges');
    }
};

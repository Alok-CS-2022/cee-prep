<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('duration_minutes')->default(180);
            $table->unsignedInteger('total_questions')->default(200);
            $table->decimal('marks_correct', 5, 2)->default(1.00);
            $table->decimal('marks_wrong', 5, 2)->default(-0.25);
            $table->decimal('marks_unanswered', 5, 2)->default(0.00);
            $table->json('subject_distribution');
            $table->json('cognitive_distribution')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_configurations');
    }
};

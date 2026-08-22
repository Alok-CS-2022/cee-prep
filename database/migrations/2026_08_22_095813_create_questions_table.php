<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->text('question');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->enum('correct_option', ['A', 'B', 'C', 'D']);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->enum('cognitive_level', ['recall', 'understanding', 'application'])->nullable();
            $table->text('explanation')->nullable();
            $table->string('source')->nullable();
            $table->string('year')->nullable();
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

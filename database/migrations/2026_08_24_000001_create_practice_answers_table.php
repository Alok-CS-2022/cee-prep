<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('selected_option', 1)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->string('confidence', 20)->nullable();
            $table->integer('time_spent_seconds')->nullable();
            $table->timestamp('answered_at');
            $table->timestamps();

            $table->index(['user_id', 'question_id']);
            $table->index(['user_id', 'is_correct']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_answers');
    }
};
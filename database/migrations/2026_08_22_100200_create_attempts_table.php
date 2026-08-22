<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'expired', 'abandoned'])->default('in_progress');
            $table->decimal('score', 6, 2)->nullable();
            $table->unsignedInteger('correct_count')->nullable();
            $table->unsignedInteger('wrong_count')->nullable();
            $table->unsignedInteger('unanswered_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamConfiguration extends Model
{
    protected $fillable = [
        'program_id',
        'name',
        'duration_minutes',
        'total_questions',
        'marks_correct',
        'marks_wrong',
        'marks_unanswered',
        'subject_distribution',
        'cognitive_distribution',
        'is_active',
    ];

    protected $casts = [
        'subject_distribution' => 'array',
        'cognitive_distribution' => 'array',
        'is_active' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}

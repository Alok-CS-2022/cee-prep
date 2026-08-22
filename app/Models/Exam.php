<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'exam_configuration_id',
        'user_id',
        'title',
        'type',
    ];

    public function examConfiguration()
    {
        return $this->belongsTo(ExamConfiguration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')->withPivot('order');
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }
}

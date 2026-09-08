<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'code',
        'subject',
        'level',
        'academic_year',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    public function learners(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'class_enrollments',
            'learning_class_id',
            'learner_id'
        )->withPivot('enrolled_at')->withTimestamps();
    }

    public function courseAssignments(): HasMany
    {
        return $this->hasMany(ClassCourseAssignment::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'class_course_assignments',
            'learning_class_id',
            'course_id'
        )->withPivot('assigned_by', 'assigned_at')->withTimestamps();
    }
}

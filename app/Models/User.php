<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = ['learner', 'parents', 'teacher', 'admin'];

    protected $fillable = [
        'name',
        'email',
        'role',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function learnerProfile(): HasOne
    {
        return $this->hasOne(LearnerProfile::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function learnerLinks(): HasMany
    {
        return $this->hasMany(ParentLearnerLink::class, 'parent_user_id');
    }

    public function parentLinks(): HasMany
    {
        return $this->hasMany(ParentLearnerLink::class, 'learner_user_id');
    }

    public function teachingClasses(): HasMany
    {
        return $this->hasMany(LearningClass::class, 'teacher_id');
    }

    public function enrolledClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            LearningClass::class,
            'class_enrollments',
            'learner_id',
            'learning_class_id'
        )->withPivot('enrolled_at')->withTimestamps();
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'learner_id');
    }

    public function courseMasteries(): HasMany
    {
        return $this->hasMany(CourseMastery::class, 'learner_id');
    }

    public function createdAssessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'created_by');
    }

    public function assessmentAttempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class, 'learner_id');
    }

    public function knowledgeSources(): HasMany
    {
        return $this->hasMany(KnowledgeSource::class, 'owner_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }
}

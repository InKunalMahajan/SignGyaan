<?php

use App\Http\Controllers\Admin\CurriculumReviewController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Learner\ClassController as LearnerClassController;
use App\Http\Controllers\Learner\LessonController;
use App\Http\Controllers\Learner\ParentLinkRequestController;
use App\Http\Controllers\Learner\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Parents\DashboardController as ParentDashboardController;
use App\Http\Controllers\Parents\LearnerLinkController;
use App\Http\Controllers\Parents\ProfileController as ParentProfileController;
use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\ClassProgressController;
use App\Http\Controllers\Teacher\CourseAssignmentController;
use App\Http\Controllers\Teacher\CurriculumController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use App\Http\Controllers\Teacher\ReviewFeedbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/guest', [DashboardController::class, 'guest'])->name('dashboard.guest');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
Route::middleware('auth')->group(function () { Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); });

Route::middleware(['auth','active'])->group(function () {
    Route::get('/dashboard/parents', ParentDashboardController::class)->middleware('parents')->name('dashboard.parents.live');
    Route::get('/dashboard/teacher', TeacherDashboardController::class)->middleware('teacher')->name('dashboard.teacher.live');
    Route::get('/dashboard/admin', AdminDashboardController::class)->middleware('admin')->name('dashboard.admin.live');
    Route::get('/dashboard/{role}', [DashboardController::class, 'show'])->whereIn('role',['learner','parents','teacher','admin'])->name('dashboard.role');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    Route::prefix('learner')->name('learner.')->middleware('learner')->group(function () {
        Route::get('/profile',[ProfileController::class,'show'])->name('profile.show'); Route::put('/profile',[ProfileController::class,'update'])->name('profile.update'); Route::post('/profile/avatar',[ProfileController::class,'updateAvatar'])->name('profile.avatar.update'); Route::delete('/profile/avatar',[ProfileController::class,'removeAvatar'])->name('profile.avatar.destroy'); Route::put('/profile/password',[ProfileController::class,'updatePassword'])->name('profile.password.update');
        Route::get('/classes',[LearnerClassController::class,'index'])->name('classes.index'); Route::get('/classes/{class}',[LearnerClassController::class,'show'])->name('classes.show'); Route::get('/classes/{class}/courses/{course}',[LearnerClassController::class,'course'])->name('classes.courses.show'); Route::get('/classes/{class}/courses/{course}/lessons/{lesson}',[LessonController::class,'show'])->name('classes.courses.lessons.show'); Route::patch('/classes/{class}/courses/{course}/lessons/{lesson}/progress',[LessonController::class,'updateProgress'])->name('classes.courses.lessons.progress.update');
        Route::get('/parent-links',[ParentLinkRequestController::class,'index'])->name('parent-links.index'); Route::patch('/parent-links/{link}',[ParentLinkRequestController::class,'respond'])->name('parent-links.respond'); Route::delete('/parent-links/{link}',[ParentLinkRequestController::class,'destroy'])->name('parent-links.destroy');
    });

    Route::prefix('parents')->name('parents.')->middleware('parents')->group(function () {
        Route::get('/profile',[ParentProfileController::class,'show'])->name('profile.show'); Route::put('/profile',[ParentProfileController::class,'update'])->name('profile.update'); Route::put('/profile/password',[ParentProfileController::class,'updatePassword'])->name('profile.password.update'); Route::post('/learners',[LearnerLinkController::class,'store'])->name('learners.store'); Route::get('/learners/{link}',[LearnerLinkController::class,'show'])->name('learners.show'); Route::delete('/learners/{link}',[LearnerLinkController::class,'destroy'])->name('learners.destroy');
    });

    Route::prefix('teacher')->name('teacher.')->middleware('teacher')->group(function () {
        Route::get('/profile',[TeacherProfileController::class,'show'])->name('profile.show'); Route::put('/profile',[TeacherProfileController::class,'update'])->name('profile.update'); Route::put('/profile/password',[TeacherProfileController::class,'updatePassword'])->name('profile.password.update');
        Route::get('/classes',[ClassController::class,'index'])->name('classes.index'); Route::get('/classes/create',[ClassController::class,'create'])->name('classes.create'); Route::post('/classes',[ClassController::class,'store'])->name('classes.store'); Route::get('/classes/{class}',[ClassController::class,'show'])->name('classes.show'); Route::get('/classes/{class}/progress',[ClassProgressController::class,'show'])->name('classes.progress'); Route::get('/classes/{class}/edit',[ClassController::class,'edit'])->name('classes.edit'); Route::put('/classes/{class}',[ClassController::class,'update'])->name('classes.update'); Route::patch('/classes/{class}/status',[ClassController::class,'toggleStatus'])->name('classes.status'); Route::post('/classes/{class}/learners',[ClassController::class,'enrollLearner'])->name('classes.learners.store'); Route::delete('/classes/{class}/learners/{learner}',[ClassController::class,'removeLearner'])->name('classes.learners.destroy'); Route::post('/classes/{class}/courses',[CourseAssignmentController::class,'store'])->name('classes.courses.store'); Route::post('/classes/{class}/courses/new',[CourseAssignmentController::class,'storeNew'])->name('classes.courses.store-new'); Route::delete('/classes/{class}/courses/{course}',[CourseAssignmentController::class,'destroy'])->name('classes.courses.destroy');
        Route::get('/reviews',[ReviewFeedbackController::class,'index'])->name('reviews.index'); Route::patch('/reviews/{lesson}/resubmit',[ReviewFeedbackController::class,'resubmit'])->name('reviews.resubmit');
        Route::get('/courses',[CurriculumController::class,'index'])->name('courses.index'); Route::get('/courses/{course}/curriculum',[CurriculumController::class,'show'])->name('courses.curriculum.show'); Route::post('/courses/{course}/units',[CurriculumController::class,'storeUnit'])->name('courses.units.store'); Route::put('/courses/{course}/units/{unit}',[CurriculumController::class,'updateUnit'])->name('courses.units.update'); Route::delete('/courses/{course}/units/{unit}',[CurriculumController::class,'destroyUnit'])->name('courses.units.destroy'); Route::post('/courses/{course}/units/{unit}/lessons',[CurriculumController::class,'storeLesson'])->name('courses.units.lessons.store'); Route::put('/courses/{course}/units/{unit}/lessons/{lesson}',[CurriculumController::class,'updateLesson'])->name('courses.units.lessons.update'); Route::delete('/courses/{course}/units/{unit}/lessons/{lesson}',[CurriculumController::class,'destroyLesson'])->name('courses.units.lessons.destroy');
    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/users',[UserManagementController::class,'index'])->name('users.index'); Route::get('/users/create',[UserManagementController::class,'create'])->name('users.create'); Route::post('/users',[UserManagementController::class,'store'])->name('users.store'); Route::get('/users/{user}',[UserManagementController::class,'show'])->name('users.show'); Route::get('/users/{user}/edit',[UserManagementController::class,'edit'])->name('users.edit'); Route::put('/users/{user}',[UserManagementController::class,'update'])->name('users.update'); Route::patch('/users/{user}/status',[UserManagementController::class,'toggleStatus'])->name('users.status');
        Route::get('/curriculum',[CurriculumReviewController::class,'index'])->name('curriculum.index'); Route::get('/curriculum/courses/{course}',[CurriculumReviewController::class,'show'])->name('curriculum.courses.show'); Route::patch('/curriculum/subjects/{subject}/status',[CurriculumReviewController::class,'toggleSubject'])->name('curriculum.subjects.status'); Route::patch('/curriculum/courses/{course}/status',[CurriculumReviewController::class,'toggleCourse'])->name('curriculum.courses.status'); Route::patch('/curriculum/lessons/{lesson}/review',[CurriculumReviewController::class,'reviewLesson'])->name('curriculum.lessons.review');
    });
});

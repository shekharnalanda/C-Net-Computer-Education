<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index'])->name('home');
Route::post('/enquiry', [EnquiryController::class,'store'])->middleware('throttle:10,1')->name('enquiry.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login',[AuthController::class,'showLogin'])->name('login');
        Route::post('/login',[AuthController::class,'login'])->name('login.submit');
    });
    Route::middleware(['auth','admin'])->group(function () {
        Route::get('/',[DashboardController::class,'index'])->name('dashboard');
        Route::post('/logout',[AuthController::class,'logout'])->name('logout');
        Route::get('/profile',[ProfileController::class,'edit'])->name('profile.edit');
        Route::put('/profile',[ProfileController::class,'update'])->name('profile.update');
        Route::put('/profile/password',[ProfileController::class,'updatePassword'])->name('profile.password');
        Route::get('/jobs',[JobController::class,'index'])->name('jobs.index');
        Route::post('/jobs',[JobController::class,'store'])->name('jobs.store');
        Route::patch('/jobs/{id}/toggle',[JobController::class,'toggle'])->name('jobs.toggle');
        Route::delete('/jobs/{id}',[JobController::class,'destroy'])->name('jobs.destroy');
        Route::get('/notices',[NoticeController::class,'index'])->name('notices.index');
        Route::post('/notices',[NoticeController::class,'store'])->name('notices.store');
        Route::patch('/notices/{id}/toggle',[NoticeController::class,'toggle'])->name('notices.toggle');
        Route::delete('/notices/{id}',[NoticeController::class,'destroy'])->name('notices.destroy');
        Route::get('/gallery',[GalleryController::class,'index'])->name('gallery.index');
        Route::post('/gallery',[GalleryController::class,'store'])->name('gallery.store');
        Route::patch('/gallery/{id}/toggle',[GalleryController::class,'toggle'])->name('gallery.toggle');
        Route::delete('/gallery/{id}',[GalleryController::class,'destroy'])->name('gallery.destroy');
        Route::get('/settings',[SettingsController::class,'edit'])->name('settings.edit');
        Route::put('/settings',[SettingsController::class,'update'])->name('settings.update');
        Route::get('/courses',[CourseController::class,'index'])->name('courses.index');
        Route::post('/courses',[CourseController::class,'store'])->name('courses.store');
        Route::put('/courses/{course}',[CourseController::class,'update'])->name('courses.update');
        Route::patch('/courses/{course}/toggle',[CourseController::class,'toggle'])->name('courses.toggle');
        Route::delete('/courses/{course}',[CourseController::class,'destroy'])->name('courses.destroy');
        Route::get('/enquiries',[AdminEnquiryController::class,'index'])->name('enquiries.index');
        Route::get('/enquiries-export',[AdminEnquiryController::class,'export'])->name('enquiries.export');
        Route::patch('/enquiries/{enquiry}/status',[AdminEnquiryController::class,'updateStatus'])->name('enquiries.status');
        Route::delete('/enquiries/{enquiry}',[AdminEnquiryController::class,'destroy'])->name('enquiries.destroy');
    });
});
Route::get('/login',fn()=>redirect()->route('admin.login'))->name('login');

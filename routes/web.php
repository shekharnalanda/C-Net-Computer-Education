<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
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
        Route::get('/courses',[CourseController::class,'index'])->name('courses.index');
        Route::post('/courses',[CourseController::class,'store'])->name('courses.store');
        Route::put('/courses/{course}',[CourseController::class,'update'])->name('courses.update');
        Route::delete('/courses/{course}',[CourseController::class,'destroy'])->name('courses.destroy');
        Route::get('/enquiries',[AdminEnquiryController::class,'index'])->name('enquiries.index');
        Route::patch('/enquiries/{enquiry}/status',[AdminEnquiryController::class,'updateStatus'])->name('enquiries.status');
        Route::delete('/enquiries/{enquiry}',[AdminEnquiryController::class,'destroy'])->name('enquiries.destroy');
    });
});
Route::get('/login',fn()=>redirect()->route('admin.login'))->name('login');

<?php

namespace App\Http\Controllers;

use App\Models\Course;

class HomeController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)->orderBy('sort_order')->orderBy('title')->get();
        return view('home', compact('courses'));
    }
}

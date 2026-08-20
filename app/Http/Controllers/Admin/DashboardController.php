<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enquiry;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'courseCount'=>Course::count(),
            'enquiryCount'=>Enquiry::count(),
            'newEnquiryCount'=>Enquiry::where('status','new')->count(),
            'recentEnquiries'=>Enquiry::latest()->limit(8)->get(),
        ]);
    }
}

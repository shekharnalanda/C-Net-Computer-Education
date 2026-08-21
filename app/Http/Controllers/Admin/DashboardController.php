<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enquiry;

class DashboardController extends Controller
{
    public function index()
    {
        $enquiryCount = Enquiry::count();
        $closedCount = Enquiry::where('status', 'closed')->count();

        return view('admin.dashboard', [
            'courseCount' => Course::count(),
            'activeCourseCount' => Course::where('is_active', true)->count(),
            'hiddenCourseCount' => Course::where('is_active', false)->count(),
            'enquiryCount' => $enquiryCount,
            'newEnquiryCount' => Enquiry::where('status', 'new')->count(),
            'contactedCount' => Enquiry::where('status', 'contacted')->count(),
            'closedCount' => $closedCount,
            'todayEnquiryCount' => Enquiry::whereDate('created_at', today())->count(),
            'monthEnquiryCount' => Enquiry::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'conversionRate' => $enquiryCount > 0 ? round(($closedCount / $enquiryCount) * 100) : 0,
            'recentEnquiries' => Enquiry::latest()->limit(6)->get(),
            'recentCourses' => Course::orderBy('sort_order')->limit(6)->get(),
        ]);
    }
}

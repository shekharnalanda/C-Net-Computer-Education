<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\JobStore;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        return view('admin.jobs.index', ['jobs' => JobStore::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'company' => ['required', 'string', 'max:140'],
            'location' => ['required', 'string', 'max:140'],
            'job_type' => ['required', 'in:Full Time,Part Time,Internship,Apprenticeship,Work From Home'],
            'qualification' => ['nullable', 'string', 'max:180'],
            'salary' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1200'],
            'apply_url' => ['required', 'url', 'max:500'],
            'posted_at' => ['required', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:posted_at'],
            'is_verified' => ['nullable', 'boolean'],
        ]);
        $data['is_verified'] = $request->boolean('is_verified');
        JobStore::add($data);

        return back()->with('success', 'Job opportunity published successfully.');
    }

    public function toggle(string $id)
    {
        abort_unless(JobStore::toggle($id), 404);

        return back()->with('success', 'Job visibility updated.');
    }

    public function destroy(string $id)
    {
        abort_unless(JobStore::remove($id), 404);

        return back()->with('success', 'Job opportunity deleted.');
    }
}

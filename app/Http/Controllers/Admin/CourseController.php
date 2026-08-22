<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = trim((string) $request->q);
                $query->where(function ($inner) use ($term): void {
                    $inner->where('code', 'like', "%{$term}%")
                        ->orWhere('title', 'like', "%{$term}%")
                        ->orWhere('title_hi', 'like', "%{$term}%")
                        ->orWhere('summary', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->status === 'active'))
            ->when($request->filled('level'), fn ($query) => $query->where('level', $request->level))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'totalCount' => Course::count(),
            'activeCount' => Course::where('is_active', true)->count(),
            'hiddenCount' => Course::where('is_active', false)->count(),
            'levels' => Course::whereNotNull('level')->distinct()->orderBy('level')->pluck('level'),
        ]);
    }

    public function store(Request $request)
    {
        Course::create($this->validated($request));

        return back()->with('success', 'Course added successfully.');
    }

    public function update(Request $request, Course $course)
    {
        $course->update($this->validated($request, $course->id));

        return back()->with('success', 'Course updated successfully.');
    }

    public function toggle(Course $course)
    {
        $course->update(['is_active' => ! $course->is_active]);

        return back()->with('success', $course->is_active ? 'Course is now visible on the website.' : 'Course is now hidden from the website.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return back()->with('success', 'Course deleted.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'code' => ['required','string','max:20','unique:courses,code'.($id ? ",{$id}" : '')],
            'title' => ['required','string','max:160'],
            'title_hi' => ['nullable','string','max:160'],
            'duration' => ['required','string','max:50'],
            'fee_amount' => ['nullable','numeric','min:0','max:99999999.99'],
            'fee_note' => ['nullable','string','max:160'],
            'level' => ['required','string','max:50'],
            'summary' => ['required','string','max:500'],
            'eligibility' => ['nullable','string','max:255'],
            'modules_text' => ['nullable','string','max:5000'],
            'careers_text' => ['nullable','string','max:5000'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['modules'] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['modules_text'] ?? ''))));
        $data['careers'] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['careers_text'] ?? ''))));
        $data['is_active'] = $request->boolean('is_active');
        unset($data['modules_text'], $data['careers_text']);

        return $data;
    }
}

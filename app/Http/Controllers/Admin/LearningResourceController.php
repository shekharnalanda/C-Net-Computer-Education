<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\LearningResourceStore;
use Illuminate\Http\Request;

class LearningResourceController extends Controller
{
    public function index(Request $request)
    {
        $search = strtolower(trim((string) $request->query('search')));
        $course = trim((string) $request->query('course'));
        $type = trim((string) $request->query('type'));
        $items = array_values(array_filter(LearningResourceStore::all(), function (array $item) use ($search, $course, $type): bool {
            $haystack = strtolower(($item['title'] ?? '').' '.($item['description'] ?? '').' '.($item['course_code'] ?? ''));
            return (! $search || str_contains($haystack, $search))
                && (! $course || ($item['course_code'] ?? '') === $course)
                && (! $type || ($item['type'] ?? '') === $type);
        }));

        return view('admin.learning.index', [
            'resources' => $items,
            'courses' => Course::orderBy('title')->get(['code','title']),
            'activeCount' => collect($items)->where('is_active', true)->count(),
            'assignmentCount' => collect($items)->where('type', 'assignment')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_code' => ['required','string','exists:courses,code'],
            'type' => ['required','in:notes,video,assignment,practice,link'],
            'title' => ['required','string','max:180'],
            'description' => ['nullable','string','max:1000'],
            'link_url' => ['required','url','max:1000','starts_with:http://,https://'],
            'due_date' => ['nullable','date'],
            'is_pinned' => ['nullable','boolean'],
        ]);
        $data['is_pinned'] = $request->boolean('is_pinned');
        LearningResourceStore::add($data);
        return back()->with('success', 'Learning resource published successfully.');
    }

    public function toggle(string $id)
    {
        abort_unless(LearningResourceStore::toggle($id), 404);
        return back()->with('success', 'Resource visibility updated.');
    }

    public function destroy(string $id)
    {
        abort_unless(LearningResourceStore::remove($id), 404);
        return back()->with('success', 'Learning resource deleted.');
    }
}

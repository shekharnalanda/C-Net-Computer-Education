<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\NoticeStore;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        return view('admin.notices.index', ['notices' => NoticeStore::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'title_hi' => ['nullable', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:800'],
            'type' => ['required', 'in:admission,course,event,holiday,important,general'],
            'notice_date' => ['required', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:notice_date'],
            'link' => ['nullable', 'url', 'max:500'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);
        $data['is_pinned'] = $request->boolean('is_pinned');
        NoticeStore::add($data);

        return back()->with('success', 'Notice published successfully.');
    }

    public function toggle(string $id)
    {
        abort_unless(NoticeStore::toggle($id), 404);

        return back()->with('success', 'Notice visibility updated.');
    }

    public function destroy(string $id)
    {
        abort_unless(NoticeStore::remove($id), 404);

        return back()->with('success', 'Notice deleted successfully.');
    }
}

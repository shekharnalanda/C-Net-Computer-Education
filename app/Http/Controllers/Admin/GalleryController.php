<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\GalleryStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        return view('admin.gallery.index', ['items' => GalleryStore::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'caption' => ['nullable', 'string', 'max:300'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $directory = public_path('uploads/gallery');
        File::ensureDirectoryExists($directory, 0755, true);
        $extension = strtolower($request->file('image')->getClientOriginalExtension());
        $filename = Str::uuid().'.'.$extension;
        $request->file('image')->move($directory, $filename);

        GalleryStore::add([
            'title' => $data['title'],
            'caption' => $data['caption'] ?? '',
            'path' => 'uploads/gallery/'.$filename,
        ]);

        return back()->with('success', 'Gallery image uploaded successfully.');
    }

    public function toggle(string $id)
    {
        abort_unless(GalleryStore::toggle($id), 404);

        return back()->with('success', 'Gallery visibility updated.');
    }

    public function destroy(string $id)
    {
        $item = GalleryStore::remove($id);
        abort_unless($item, 404);

        $relative = (string) ($item['path'] ?? '');
        if (str_starts_with($relative, 'uploads/gallery/')) {
            $path = public_path($relative);
            if (is_file($path)) @unlink($path);
        }

        return back()->with('success', 'Gallery image deleted.');
    }
}

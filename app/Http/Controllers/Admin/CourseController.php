<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index() { return view('admin.courses.index',['courses'=>Course::orderBy('sort_order')->get()]); }
    public function store(Request $request)
    {
        Course::create($this->validated($request));
        return back()->with('success','Course added successfully.');
    }
    public function update(Request $request, Course $course)
    {
        $course->update($this->validated($request, $course->id));
        return back()->with('success','Course updated successfully.');
    }
    public function destroy(Course $course)
    {
        $course->delete(); return back()->with('success','Course deleted.');
    }
    private function validated(Request $request, ?int $id=null): array
    {
        $data=$request->validate([
            'code'=>['required','string','max:20','unique:courses,code'.($id?",{$id}":'')],
            'title'=>['required','string','max:160'],'title_hi'=>['nullable','string','max:160'],
            'duration'=>['required','string','max:50'],'level'=>['required','string','max:50'],
            'summary'=>['required','string','max:500'],'eligibility'=>['nullable','string','max:255'],
            'modules_text'=>['nullable','string'],'careers_text'=>['nullable','string'],
            'sort_order'=>['nullable','integer','min:0'],'is_active'=>['nullable','boolean'],
        ]);
        $data['modules']=array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',$data['modules_text'] ?? ''))));
        $data['careers']=array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',$data['careers_text'] ?? ''))));
        $data['is_active']=$request->boolean('is_active'); unset($data['modules_text'],$data['careers_text']);
        return $data;
    }
}

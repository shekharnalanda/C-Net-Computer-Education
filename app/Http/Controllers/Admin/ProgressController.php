<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\AdmissionStore;
use App\Support\SiteSettings;
use App\Support\StudentProgress;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $search=strtolower(trim((string)$request->query('search')));
        $course=trim((string)$request->query('course'));
        $level=trim((string)$request->query('level'));
        $students=array_map(function(array $student):array{
            $student['progress']=StudentProgress::calculate($student); return $student;
        },array_values(array_filter(AdmissionStore::all(),fn(array $s):bool=>($s['status']??'')==='admitted')));
        $students=array_values(array_filter($students,function(array $student)use($search,$course,$level):bool{
            $haystack=strtolower(($student['student_name']??'').' '.($student['application_no']??'').' '.($student['roll_no']??''));
            $score=$student['progress']['overall'];
            $levelMatch=!$level||($level==='high'&&$score>=70)||($level==='medium'&&$score>=40&&$score<70)||($level==='low'&&$score<40);
            return(!$search||str_contains($haystack,$search))&&(!$course||($student['course_code']??'')===$course)&&$levelMatch;
        }));
        return view('admin.progress.index',[
            'students'=>$students,'courses'=>Course::orderBy('title')->get(['code','title']),
            'average'=>count($students)?round(collect($students)->avg('progress.overall'),1):0,
            'highPerformers'=>collect($students)->filter(fn($s)=>$s['progress']['overall']>=70)->count(),
            'needsSupport'=>collect($students)->filter(fn($s)=>$s['progress']['overall']<40)->count(),
        ]);
    }

    public function print(string $id)
    {
        $student=AdmissionStore::find($id);
        abort_unless($student&&($student['status']??'')==='admitted',404);
        return view('admin.progress.print',[
            'student'=>$student,'progress'=>StudentProgress::calculate($student),
            'course'=>Course::where('code',$student['course_code']??'')->first(),
            'settings'=>SiteSettings::all(),
        ]);
    }
}

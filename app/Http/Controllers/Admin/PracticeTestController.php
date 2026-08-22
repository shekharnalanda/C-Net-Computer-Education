<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\AdmissionStore;
use App\Support\PracticeTestStore;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PracticeTestController extends Controller
{
    public function index(Request $request)
    {
        $starterCount=PracticeTestStore::installStarterSets(Course::where('is_active',true)->pluck('code')->all());
        $search=strtolower(trim((string)$request->query('search')));
        $course=trim((string)$request->query('course'));
        $attempts=PracticeTestStore::attempts();
        $students=collect(AdmissionStore::all())->keyBy('id');
        $tests=array_values(array_filter(PracticeTestStore::all(),function(array $test)use($search,$course):bool{
            return (!$search||str_contains(strtolower(($test['title']??'').' '.($test['course_code']??'')),$search))
                &&(!$course||($test['course_code']??'')===$course);
        }));
        foreach($tests as &$test){
            $test['attempts']=array_values(array_filter($attempts,fn(array $row):bool=>($row['test_id']??'')===$test['id']));
        } unset($test);
        foreach($attempts as &$attempt){$attempt['student']=$students->get($attempt['student_id']);} unset($attempt);
        return view('admin.practice-tests.index',[
            'tests'=>$tests,'attempts'=>array_slice($attempts,0,50),
            'courses'=>Course::orderBy('title')->get(['code','title']),
            'activeCount'=>collect($tests)->where('is_active',true)->count(),
            'starterCount'=>$starterCount,
            'attemptCount'=>count($attempts),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'assessment_type'=>$request->input('assessment_type','practice'),
            'assessment_weight'=>$request->input('assessment_weight',20),
            'assessment_order'=>$request->input('assessment_order',1),
        ]);
        $questions=array_values(array_filter((array)$request->input('questions',[]),fn($q):bool=>trim((string)($q['prompt']??''))!==''));
        $request->merge(['questions'=>$questions]);
        $data=$request->validate([
            'course_code'=>['required','string','exists:courses,code'],
            'title'=>['required','string','max:180'],
            'duration_minutes'=>['required','integer','min:1','max:180'],
            'pass_percentage'=>['required','numeric','min:1','max:100'],
            'assessment_type'=>['required','in:practice,terminal,final'],
            'assessment_weight'=>['required','numeric','min:1','max:100'],
            'assessment_order'=>['required','integer','min:1','max:5'],
            'questions'=>['required','array','min:1','max:20'],
            'questions.*.prompt'=>['required','string','max:500'],
            'questions.*.option_a'=>['required','string','max:250'],
            'questions.*.option_b'=>['required','string','max:250'],
            'questions.*.option_c'=>['required','string','max:250'],
            'questions.*.option_d'=>['required','string','max:250'],
            'questions.*.correct'=>['required','in:A,B,C,D'],
        ]);
        $data['questions']=array_map(function(array $q):array{
            return ['id'=>(string)Str::uuid(),'prompt'=>$q['prompt'],'options'=>[
                'A'=>$q['option_a'],'B'=>$q['option_b'],'C'=>$q['option_c'],'D'=>$q['option_d'],
            ],'correct'=>$q['correct']];
        },$data['questions']);
        PracticeTestStore::add($data);
        return back()->with('success','Practice test published successfully.');
    }

    public function toggle(string $id)
    {
        abort_unless(PracticeTestStore::toggle($id),404);
        return back()->with('success','Practice test visibility updated.');
    }

    public function destroy(string $id)
    {
        abort_unless(PracticeTestStore::remove($id),404);
        return back()->with('success','Practice test and its attempts deleted.');
    }
}

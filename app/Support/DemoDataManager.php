<?php

namespace App\Support;

use App\Models\Course;

class DemoDataManager
{
    private const NAMES=['Aarav Kumar','Ananya Singh','Aditya Raj','Aditi Kumari','Ayush Sharma','Ishita Verma','Kunal Kumar','Khushi Singh','Manish Raj','Muskan Kumari','Nikhil Sharma','Neha Verma','Prashant Kumar','Priya Singh','Rahul Raj','Riya Kumari','Rohit Sharma','Sakshi Verma','Saurav Kumar','Simran Singh','Vikas Raj','Vandana Kumari','Yash Sharma','Zoya Parveen'];

    public static function install(): array
    {
        $courses=Course::where('is_active',true)->orderBy('sort_order')->get(['code','title','fee_amount']);
        PracticeTestStore::installStarterSets($courses->pluck('code')->all());
        $existing=collect(AdmissionStore::all())->filter(fn(array $item):bool=>(bool)($item['is_demo']??false));
        $created=0;
        foreach($courses as $courseIndex=>$course){
            $courseExisting=$existing->where('course_code',$course->code)->count();
            for($slot=$courseExisting;$slot<5;$slot++){
                $serial=($courseIndex*5)+$slot;
                $student=AdmissionStore::add([
                    'is_demo'=>true,'student_name'=>self::NAMES[$serial%count(self::NAMES)].' (Demo)',
                    'guardian_name'=>'Demo Guardian','phone'=>'98'.str_pad((string)(70000000+$serial),8,'0',STR_PAD_LEFT),
                    'email'=>'demo'.($serial+1).'@example.invalid','city'=>'Bihar Sharif','course_code'=>$course->code,
                    'course_fee'=>0,'dob'=>'2004-01-'.str_pad((string)(($serial%28)+1),2,'0',STR_PAD_LEFT),
                    'gender'=>$serial%2?'Female':'Male','qualification'=>'12th','address'=>'Demo Record — Bihar Sharif',
                    'preferred_time'=>['Morning','Afternoon','Evening'][$serial%3],'message'=>'System-generated demonstration student.',
                ]);
                AdmissionStore::updateStatus($student['id'],'admitted');
                AdmissionStore::updateStudentRecord($student['id'],[
                    'roll_no'=>'DEMO-'.strtoupper($course->code).'-'.str_pad((string)($slot+1),2,'0',STR_PAD_LEFT),
                    'batch_name'=>'Demo Batch','batch_time'=>['09:00 AM','01:00 PM','04:00 PM'][$slot%3],
                    'joining_date'=>now()->subMonths(6)->addDays($serial)->toDateString(),'student_status'=>'completed',
                ]);
                $student=AdmissionStore::find($student['id']);
                $tests=array_values(array_filter(PracticeTestStore::all(),fn(array $test):bool=>($test['course_code']??'')===$course->code&&isset($test['assessment_order'])));
                foreach($tests as $test){
                    $percentage=68+(($serial+(int)$test['assessment_order']*5)%29);
                    $total=count($test['questions']); $correct=(int)round(($percentage/100)*$total);
                    PracticeTestStore::recordAttempt([
                        'is_demo'=>true,'test_id'=>$test['id'],'student_id'=>$student['id'],'course_code'=>$course->code,
                        'correct_answers'=>$correct,'total_questions'=>$total,'percentage'=>$percentage,'status'=>'pass','review'=>[],
                    ]);
                }
                CourseAssessment::publishIfEligible($student); $created++;
            }
        }
        return self::status()+['created'=>$created];
    }

    public static function remove(): array
    {
        $ids=AdmissionStore::removeDemoData();
        $attempts=PracticeTestStore::removeAttemptsForStudents($ids);
        $results=ExamResultStore::removeForStudents($ids);
        $certificates=CertificateStore::removeForStudents($ids);
        return compact('attempts','results','certificates')+['students'=>count($ids)];
    }

    public static function status(): array
    {
        $ids=collect(AdmissionStore::all())->filter(fn(array $item):bool=>(bool)($item['is_demo']??false))->pluck('id')->all();
        return [
            'students'=>count($ids),
            'attempts'=>collect(PracticeTestStore::attempts())->whereIn('student_id',$ids)->count(),
            'results'=>collect(ExamResultStore::all())->whereIn('student_id',$ids)->count(),
            'certificates'=>collect(CertificateStore::all())->whereIn('student_id',$ids)->count(),
        ];
    }
}

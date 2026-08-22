<?php

namespace App\Support;

use App\Models\Course;

class CourseAssessment
{
    public static function summary(array $student): array
    {
        $tests=array_values(array_filter(PracticeTestStore::all(),fn(array $test):bool=>
            ($test['is_active']??true)
            &&($test['course_code']??'')===($student['course_code']??'')
            &&isset($test['assessment_order'])
        ));
        usort($tests,fn(array $a,array $b):int=>($a['assessment_order']??99)<=>($b['assessment_order']??99));
        $attempts=collect(PracticeTestStore::attemptsForStudent($student['id']));
        $rows=[]; $weighted=0; $weightDone=0;
        foreach($tests as $test){
            $best=$attempts->where('test_id',$test['id'])->sortByDesc('percentage')->first();
            $weight=(float)($test['assessment_weight']??0);
            if($best){$weighted+=((float)$best['percentage']*$weight)/100;$weightDone+=$weight;}
            $rows[]=['test'=>$test,'attempt'=>$best,'weight'=>$weight];
        }
        $required=min(5,count($rows));
        $completed=collect($rows)->filter(fn(array $row):bool=>(bool)$row['attempt'])->count();
        $finalRow=collect($rows)->first(fn(array $row):bool=>($row['test']['assessment_type']??'')==='final');
        $complete=$required===5&&$completed>=5;
        $percentage=round($weighted,2);
        $finalPassed=$finalRow&&($finalRow['attempt']['status']??'fail')==='pass';
        $passed=$complete&&$percentage>=40&&$finalPassed;
        return compact('rows','required','completed','complete','percentage','passed')+['grade'=>self::grade($percentage,$passed)];
    }

    public static function publishIfEligible(array $student): array
    {
        $summary=self::summary($student);
        if(!$summary['passed']) return $summary;
        $source='course-assessment:'.$student['id'].':'.($student['course_code']??'');
        $result=collect(ExamResultStore::all())->firstWhere('source',$source);
        if(!$result){
            $subjects=array_map(fn(array $row):array=>[
                'name'=>$row['test']['title'],'max_marks'=>100,
                'obtained_marks'=>(float)$row['attempt']['percentage'],
                'status'=>$row['attempt']['status'],
            ],$summary['rows']);
            $result=ExamResultStore::add([
                'source'=>$source,'student_id'=>$student['id'],'exam_name'=>'Course Final Assessment',
                'exam_date'=>now()->toDateString(),'subjects'=>$subjects,'max_total'=>500,
                'obtained_total'=>(float)collect($subjects)->sum('obtained_marks'),
                'percentage'=>$summary['percentage'],'grade'=>$summary['grade'],'result_status'=>'pass',
                'remarks'=>'Automatically generated from two practice tests, two terminal tests and the final test.',
            ]);
        }
        $certificate=collect(CertificateStore::all())->firstWhere('source',$source);
        if(!$certificate){
            $course=Course::where('code',$student['course_code']??'')->first();
            $certificate=CertificateStore::add([
                'source'=>$source,'student_id'=>$student['id'],'type'=>'completion',
                'title'=>'Certificate of Course Completion','issue_date'=>now()->toDateString(),
                'completion_date'=>now()->toDateString(),'grade'=>$summary['grade'],
                'description'=>'Successfully completed '.($course?->title??($student['course_code']??'the course')).' with '.$summary['percentage'].'%.',
            ]);
        }
        return $summary+['result'=>$result,'certificate'=>$certificate];
    }

    private static function grade(float $percentage,bool $passed): string
    {
        if(!$passed) return 'F';
        return match(true){$percentage>=90=>'A+',$percentage>=80=>'A',$percentage>=70=>'B+',$percentage>=60=>'B',$percentage>=50=>'C',default=>'D'};
    }
}

<?php

namespace App\Support;

class StudentProgress
{
    public static function calculate(array $student): array
    {
        $id=$student['id'];
        $attendance=array_values(array_filter(AttendanceStore::all(),fn(array $r):bool=>($r['student_id']??'')===$id));
        $present=count(array_filter($attendance,fn(array $r):bool=>($r['status']??'')==='present'));
        $attendanceRate=count($attendance)?round(($present/count($attendance))*100,1):null;

        $assignments=array_values(array_filter(AssignmentSubmissionStore::forStudent($id),fn(array $r):bool=>($r['status']??'')==='reviewed'&&is_numeric($r['marks']??null)));
        $assignmentAverage=count($assignments)?round(array_sum(array_column($assignments,'marks'))/count($assignments),1):null;

        $latestAttempts=[];
        foreach(PracticeTestStore::attemptsForStudent($id) as $attempt){
            if(!isset($latestAttempts[$attempt['test_id']]))$latestAttempts[$attempt['test_id']]=$attempt;
        }
        $practiceAverage=count($latestAttempts)?round(array_sum(array_column($latestAttempts,'percentage'))/count($latestAttempts),1):null;

        $results=array_values(array_filter(ExamResultStore::all(),fn(array $r):bool=>($r['student_id']??'')===$id));
        $examAverage=count($results)?round(array_sum(array_column($results,'percentage'))/count($results),1):null;

        $metrics=array_values(array_filter([$attendanceRate,$assignmentAverage,$practiceAverage,$examAverage],fn($v):bool=>$v!==null));
        $overall=count($metrics)?round(array_sum($metrics)/count($metrics),1):0;
        $fee=(float)($student['course_fee']??0); $paid=(float)($student['paid_amount']??0); $balance=(float)($student['balance_amount']??max(0,$fee-$paid));

        return [
            'attendance_rate'=>$attendanceRate,'attendance_total'=>count($attendance),
            'assignment_average'=>$assignmentAverage,'assignment_count'=>count($assignments),
            'practice_average'=>$practiceAverage,'practice_count'=>count($latestAttempts),
            'exam_average'=>$examAverage,'exam_count'=>count($results),
            'overall'=>$overall,'grade'=>self::grade($overall),
            'course_fee'=>$fee,'paid_amount'=>$paid,'balance_amount'=>$balance,
            'fee_percentage'=>$fee>0?round(min(100,($paid/$fee)*100),1):100,
        ];
    }

    private static function grade(float $score): string
    {
        return match(true){$score>=85=>'Excellent',$score>=70=>'Very Good',$score>=55=>'Good',$score>=40=>'Needs Practice',default=>'Getting Started'};
    }
}

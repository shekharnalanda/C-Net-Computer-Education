<?php

namespace App\Support;

class StudentNotificationCenter
{
    public static function forStudent(array $student): array
    {
        $items=[];
        foreach(array_slice(NoticeStore::published(),0,5) as $notice){
            $items[]=['type'=>'notice','title'=>$notice['title'],'title_hi'=>$notice['title_hi']??'','message'=>$notice['description']??'','date'=>$notice['notice_date']??'','link'=>$notice['link']??''];
        }
        $balance=(float)($student['balance_amount']??max(0,(float)($student['course_fee']??0)-(float)($student['paid_amount']??0)));
        if($balance>0)$items[]=['type'=>'fee','title'=>'Fee payment reminder','title_hi'=>'शुल्क भुगतान स्मरण','message'=>'Pending balance: ₹'.number_format($balance,2),'date'=>now()->toDateString(),'link'=>'#fees'];

        $submissions=collect(AssignmentSubmissionStore::forStudent($student['id']))->keyBy('resource_id');
        $assignments=array_values(array_filter(LearningResourceStore::all(),fn(array $r):bool=>($r['is_active']??true)&&($r['type']??'')==='assignment'&&($r['course_code']??'')===($student['course_code']??'')));
        foreach($assignments as $assignment){
            $submission=$submissions->get($assignment['id']);
            if(!$submission)$items[]=['type'=>'assignment','title'=>'Pending: '.$assignment['title'],'title_hi'=>'Assignment जमा करें','message'=>$assignment['due_date']?'Due date: '.$assignment['due_date']:'Submit from Study Materials.','date'=>$assignment['created_at']??'','link'=>'#learning'];
            elseif(($submission['status']??'')==='reviewed')$items[]=['type'=>'success','title'=>'Assignment reviewed: '.$assignment['title'],'title_hi'=>'Assignment जाँच पूरी','message'=>'Marks: '.$submission['marks'].'/100'.($submission['feedback']?' · '.$submission['feedback']:''),'date'=>$submission['reviewed_at']??'','link'=>'#learning'];
        }

        $results=array_values(array_filter(ExamResultStore::all(),fn(array $r):bool=>($r['student_id']??'')===$student['id']));
        if(count($results)){ $latest=$results[0]; $items[]=['type'=>'result','title'=>'Result available: '.$latest['exam_name'],'title_hi'=>'नया परीक्षा परिणाम','message'=>number_format($latest['percentage'],1).'% · Grade '.$latest['grade'],'date'=>$latest['created_at']??$latest['exam_date'],'link'=>'#results']; }

        return array_slice($items,0,12);
    }
}

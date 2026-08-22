<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\AdmissionStore;
use App\Support\CommunicationTemplateStore;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function index(Request $request)
    {
        $templates=CommunicationTemplateStore::all();
        $selected=CommunicationTemplateStore::find((string)$request->query('template'))??($templates[0]??null);
        $search=strtolower(trim((string)$request->query('search'))); $course=trim((string)$request->query('course')); $dues=$request->boolean('dues');
        $students=array_values(array_filter(array_map(function(array $s)use($selected):array{
            $fee=(float)($s['course_fee']??0);$paid=(float)($s['paid_amount']??0);$s['balance_amount']=(float)($s['balance_amount']??max(0,$fee-$paid));
            $s['rendered']=$selected?CommunicationTemplateStore::render($selected,$s):['subject'=>'','body'=>''];
            $phone=preg_replace('/\D+/','',(string)($s['phone']??''));$s['whatsapp_phone']=strlen($phone)===10?'91'.$phone:$phone;
            return $s;
        },array_filter(AdmissionStore::all(),fn(array $s):bool=>($s['status']??'')==='admitted')),function(array $s)use($search,$course,$dues):bool{
            $haystack=strtolower(($s['student_name']??'').' '.($s['application_no']??'').' '.($s['phone']??''));
            return(!$search||str_contains($haystack,$search))&&(!$course||($s['course_code']??'')===$course)&&(!$dues||$s['balance_amount']>0);
        }));
        return view('admin.communications.index',[
            'templates'=>$templates,'selected'=>$selected,'students'=>$students,
            'courses'=>Course::orderBy('title')->get(['code','title']),
        ]);
    }

    public function store(Request $request)
    {
        $data=$request->validate([
            'name'=>['required','string','max:120'],'channel'=>['required','in:whatsapp,email,both'],
            'category'=>['required','in:fee,assignment,result,general'],'subject'=>['required','string','max:180'],
            'body'=>['required','string','max:2000'],
        ]);
        CommunicationTemplateStore::add($data);
        return back()->with('success','Communication template saved.');
    }

    public function destroy(string $id)
    {
        abort_unless(CommunicationTemplateStore::remove($id),404);
        return redirect()->route('admin.communications.index')->with('success','Communication template deleted.');
    }
}

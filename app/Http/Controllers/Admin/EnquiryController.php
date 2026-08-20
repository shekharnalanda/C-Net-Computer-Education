<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $enquiries=Enquiry::when($request->q,function($q,$term){$q->where(function($x) use($term){$x->where('name','like',"%{$term}%")->orWhere('phone','like',"%{$term}%")->orWhere('course_code','like',"%{$term}%");});})->latest()->paginate(25)->withQueryString();
        return view('admin.enquiries.index',compact('enquiries'));
    }
    public function updateStatus(Request $request, Enquiry $enquiry)
    {
        $data=$request->validate(['status'=>['required','in:new,contacted,closed']]); $enquiry->update($data);
        return back()->with('success','Enquiry status updated.');
    }
    public function destroy(Enquiry $enquiry) { $enquiry->delete(); return back()->with('success','Enquiry deleted.'); }
}

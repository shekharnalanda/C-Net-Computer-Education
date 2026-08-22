<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoDataManager;

class DemoDataController extends Controller
{
    public function index(){return view('admin.demo-data.index',['status'=>DemoDataManager::status()]);}
    public function install(){
        $status=DemoDataManager::install();
        return back()->with('success',$status['created'].' demo students added. Demo assessments, marksheets and SAMPLE certificates are ready.');
    }
    public function destroy(){
        $removed=DemoDataManager::remove();
        return back()->with('success',$removed['students'].' demo students and their generated records removed.');
    }
}

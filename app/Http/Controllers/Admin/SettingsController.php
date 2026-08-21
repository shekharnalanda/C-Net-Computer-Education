<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
class SettingsController extends Controller
{
    public function edit(){ return view('admin.settings.edit',['settings'=>SiteSettings::all()]); }
    public function update(Request $request)
    {
        $data=$request->validate([
            'phone'=>['required','string','max:30'],'whatsapp'=>['required','regex:/^[0-9]{10,15}$/'],
            'email'=>['required','email','max:190'],'address_line'=>['required','string','max:190'],
            'city'=>['required','string','max:100'],'district'=>['required','string','max:100'],
            'state'=>['required','string','max:100'],'pin'=>['required','regex:/^[0-9]{6}$/'],
            'job_location'=>['required','string','max:100'],'job_role'=>['required','string','max:100'],
        ]);
        SiteSettings::update($data);
        return back()->with('success','Website settings updated successfully.');
    }
}

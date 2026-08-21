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
            'admission_notice'=>['required','string','max:100'],
            'hero_title'=>['required','string','max:100'],'hero_highlight'=>['required','string','max:100'],
            'hero_text_en'=>['required','string','max:400'],'hero_text_hi'=>['required','string','max:400'],
            'highlight_two_value'=>['required','string','max:30'],'highlight_two_label'=>['required','string','max:60'],
            'highlight_three_value'=>['required','string','max:30'],'highlight_three_label'=>['required','string','max:60'],
            'why_title'=>['required','string','max:100'],'why_highlight'=>['required','string','max:100'],
            'why_lead'=>['required','string','max:300'],
        ]);
        SiteSettings::update($data);
        return back()->with('success','Website settings updated successfully.');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = env('ADMIN_PASSWORD');
        if (!$adminPassword || $adminPassword === 'change-this-before-seeding') {
            throw new \RuntimeException('Set a secure ADMIN_PASSWORD in .env before seeding.');
        }
        User::updateOrCreate(['email'=>env('ADMIN_EMAIL','admin@example.com')],[
            'name'=>env('ADMIN_NAME','Administrator'),'password'=>Hash::make($adminPassword),'is_admin'=>true,
        ]);

        $courses = [
            ['DCA','Diploma in Computer Applications','कंप्यूटर एप्लीकेशन डिप्लोमा','6 Months','Foundation','MS Office · Internet · Typing · Digital Services','10th pass or equivalent'],
            ['ADCA','Advanced Diploma in Computer Applications','एडवांस कंप्यूटर एप्लीकेशन','12 Months','Career','Advanced Office · Tally · DTP · Web Basics','10th/12th pass'],
            ['CCC','Course on Computer Concepts','कंप्यूटर कॉन्सेप्ट कोर्स','3 Months','Foundation','Digital Literacy · Office Tools · Email · Cyber Safety','Open to all learners'],
            ['TALLY','Tally Prime with GST','टैली प्राइम एवं जीएसटी','3–6 Months','Job-ready','Accounting · Inventory · GST · Payroll · Reports','10th/12th pass'],
            ['EXCEL','Advanced Excel & MIS','एडवांस एक्सेल और MIS','2 Months','Job-ready','Formulas · Dashboards · Pivot Tables · MIS Reports','Basic computer knowledge'],
            ['DTP','DTP & Graphic Design','डीटीपी एवं ग्राफिक डिजाइन','6 Months','Creative','Photoshop · CorelDRAW · Page Layout · Branding','10th pass'],
            ['WEB','Web Design & Development','वेब डिजाइन एवं डेवलपमेंट','6 Months','Technical','HTML · CSS · JavaScript · Responsive Projects','10th/12th pass'],
            ['PYTHON','Python Programming','पायथन प्रोग्रामिंग','4 Months','Technical','Logic · Python · Automation · Data · Projects','12th pass recommended'],
            ['DIGITAL','Digital Marketing','डिजिटल मार्केटिंग','3 Months','Career','SEO · Social Media · Content · Ads · Analytics','10th/12th pass'],
            ['HARDWARE','Hardware & Networking','हार्डवेयर एवं नेटवर्किंग','6 Months','Technical','PC Assembly · OS · LAN · Routers · Security','10th/12th pass'],
            ['AI','AI Tools for Study & Work','पढ़ाई और काम के लिए AI Tools','1 Month','Future Skill','Prompting · Research · Productivity · Responsible AI','Basic computer knowledge'],
            ['DATA','Data Entry & Office Assistant','डेटा एंट्री एवं ऑफिस असिस्टेंट','3 Months','Job-ready','Typing · Documents · Spreadsheets · Communication','10th pass'],
        ];
        foreach($courses as $i=>$c) Course::updateOrCreate(['code'=>$c[0]],['title'=>$c[1],'title_hi'=>$c[2],'duration'=>$c[3],'level'=>$c[4],'summary'=>$c[5],'eligibility'=>$c[6],'modules'=>[$c[5]],'careers'=>[],'sort_order'=>$i+1,'is_active'=>true]);
    }
}

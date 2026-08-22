<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Support\AdmissionStore;
use App\Support\CertificateStore;
use App\Support\SiteSettings;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function index(Request $request)
    {
        $code = strtoupper(trim((string) $request->query('code')));
        $certificate = $code ? CertificateStore::findByCode($code) : null;
        if($certificate['is_demo']??false) $certificate=null;
        $student = $certificate ? AdmissionStore::find($certificate['student_id']) : null;

        return view('certificates.verify', [
            'code' => $code,
            'searched' => $request->has('code'),
            'certificate' => $certificate,
            'student' => $student,
            'course' => $student ? Course::where('code', $student['course_code'] ?? '')->first() : null,
            'settings' => SiteSettings::all(),
        ]);
    }
}

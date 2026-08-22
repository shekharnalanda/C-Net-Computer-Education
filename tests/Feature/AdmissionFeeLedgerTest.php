<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Support\AdmissionStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdmissionFeeLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::disk('local')->delete('cnet-admissions.json');
    }

    protected function tearDown(): void
    {
        Storage::disk('local')->delete('cnet-admissions.json');
        parent::tearDown();
    }

    public function test_application_snapshots_course_fee_and_admin_can_record_payment(): void
    {
        Mail::fake();
        Course::create([
            'code' => 'DCA',
            'title' => 'Diploma in Computer Applications',
            'duration' => '6 Months',
            'fee_amount' => 4500,
            'fee_note' => 'Registration included',
            'level' => 'Foundation',
            'summary' => 'Office skills',
            'is_active' => true,
        ]);

        $this->post('/apply-online', [
            'student_name' => 'Test Student',
            'dob' => '2005-01-01',
            'gender' => 'Male',
            'guardian_name' => 'Test Guardian',
            'phone' => '9876543210',
            'email' => 'student@example.com',
            'address' => 'Bihar Sharif',
            'city' => 'Bihar Sharif',
            'qualification' => '12th',
            'course_code' => 'DCA',
            'preferred_time' => 'Morning',
            'message' => '',
            'website' => '',
        ])->assertRedirect();

        $application = AdmissionStore::all()[0];
        $this->assertSame(4500.0, (float) $application['course_fee']);
        $this->assertSame('unpaid', $application['payment_status']);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->patch(route('admin.admissions.payment', $application['id']), [
            'course_fee' => 4500,
            'paid_amount' => 2000,
            'payment_note' => 'UPI first instalment',
        ])->assertRedirect();

        $updated = AdmissionStore::all()[0];
        $this->assertSame(2000.0, (float) $updated['paid_amount']);
        $this->assertSame(2500.0, (float) $updated['balance_amount']);
        $this->assertSame('partial', $updated['payment_status']);
        $this->assertNotEmpty($updated['receipt_no']);
    }
}

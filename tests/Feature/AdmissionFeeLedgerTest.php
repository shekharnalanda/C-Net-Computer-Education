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

    public function test_admin_can_open_a_printable_fee_receipt(): void
    {
        Course::create([
            'code' => 'DCA',
            'title' => 'Diploma in Computer Applications',
            'duration' => '6 Months',
            'fee_amount' => 4500,
            'level' => 'Foundation',
            'summary' => 'Office skills',
            'is_active' => true,
        ]);
        $application = AdmissionStore::add([
            'student_name' => 'Receipt Student',
            'guardian_name' => 'Receipt Guardian',
            'phone' => '9876543210',
            'city' => 'Bihar Sharif',
            'course_code' => 'DCA',
            'course_fee' => 4500,
            'dob' => '2005-01-01',
            'gender' => 'Male',
            'qualification' => '12th',
            'address' => 'Bihar Sharif',
            'email' => '',
            'preferred_time' => 'Morning',
            'message' => '',
        ]);
        AdmissionStore::updatePayment($application['id'], 4500, 2000, 'Cash first instalment');

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->get(route('admin.admissions.receipt', $application['id']))
            ->assertOk()
            ->assertSee('FEE RECEIPT')
            ->assertSee('Receipt Student')
            ->assertSee('2,000.00')
            ->assertSee('Print / Save PDF');
    }


    public function test_admitted_student_appears_in_register_and_card(): void
    {
        Course::create([
            'code' => 'DCA',
            'title' => 'Diploma in Computer Applications',
            'duration' => '6 Months',
            'fee_amount' => 4500,
            'level' => 'Foundation',
            'summary' => 'Office skills',
            'is_active' => true,
        ]);
        $student = AdmissionStore::add([
            'student_name' => 'Admitted Student',
            'guardian_name' => 'Student Guardian',
            'phone' => '9876543210',
            'city' => 'Bihar Sharif',
            'course_code' => 'DCA',
            'course_fee' => 4500,
            'dob' => '2005-01-01',
            'gender' => 'Male',
            'qualification' => '12th',
            'address' => 'Bihar Sharif',
            'email' => '',
            'preferred_time' => 'Morning',
            'message' => '',
        ]);
        AdmissionStore::updateStatus($student['id'], 'admitted');

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.students.index'))
            ->assertOk()->assertSee('Admitted Student')->assertSee('Students Register');

        $this->get(route('admin.students.card', $student['id']))
            ->assertOk()->assertSee('STUDENT ADMISSION CARD')->assertSee('Admitted Student');
    }


    public function test_admin_can_assign_roll_number_and_batch(): void
    {
        Course::create([
            'code' => 'DCA',
            'title' => 'Diploma in Computer Applications',
            'duration' => '6 Months',
            'fee_amount' => 4500,
            'level' => 'Foundation',
            'summary' => 'Office skills',
            'is_active' => true,
        ]);
        $student = AdmissionStore::add([
            'student_name' => 'Batch Student',
            'guardian_name' => 'Batch Guardian',
            'phone' => '9876543210',
            'city' => 'Bihar Sharif',
            'course_code' => 'DCA',
            'course_fee' => 4500,
            'dob' => '2005-01-01',
            'gender' => 'Male',
            'qualification' => '12th',
            'address' => 'Bihar Sharif',
            'email' => '',
            'preferred_time' => 'Morning',
            'message' => '',
        ]);
        AdmissionStore::updateStatus($student['id'], 'admitted');

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->patch(route('admin.students.update', $student['id']), [
            'roll_no' => 'CNET-DCA-001',
            'batch_name' => 'DCA Morning Batch',
            'batch_time' => '08:00 AM - 10:00 AM',
            'joining_date' => '2026-08-22',
            'student_status' => 'active',
        ])->assertRedirect();

        $updated = AdmissionStore::find($student['id']);
        $this->assertSame('CNET-DCA-001', $updated['roll_no']);
        $this->assertSame('DCA Morning Batch', $updated['batch_name']);

        $this->get(route('admin.students.card', $student['id']))
            ->assertOk()->assertSee('CNET-DCA-001')->assertSee('DCA Morning Batch');
    }


    public function test_admin_can_record_multiple_installments_and_print_each_receipt(): void
    {
        Course::create([
            'code' => 'DCA', 'title' => 'Diploma in Computer Applications',
            'duration' => '6 Months', 'fee_amount' => 4500,
            'level' => 'Foundation', 'summary' => 'Office skills', 'is_active' => true,
        ]);
        $application = AdmissionStore::add([
            'student_name' => 'Installment Student', 'guardian_name' => 'Guardian',
            'phone' => '9876543210', 'city' => 'Bihar Sharif', 'course_code' => 'DCA',
            'course_fee' => 4500, 'dob' => '2005-01-01', 'gender' => 'Male',
            'qualification' => '12th', 'address' => 'Bihar Sharif', 'email' => '',
            'preferred_time' => 'Morning', 'message' => '',
        ]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.admissions.payments.store', $application['id']), [
            'amount' => 1500, 'payment_date' => '2026-08-22', 'mode' => 'cash',
            'reference' => '', 'note' => 'First installment',
        ])->assertRedirect();
        $this->post(route('admin.admissions.payments.store', $application['id']), [
            'amount' => 1000, 'payment_date' => '2026-08-22', 'mode' => 'upi',
            'reference' => 'UPI-12345', 'note' => 'Second installment',
        ])->assertRedirect();

        $updated = AdmissionStore::find($application['id']);
        $this->assertCount(2, $updated['payments']);
        $this->assertNotSame($updated['payments'][0]['receipt_no'], $updated['payments'][1]['receipt_no']);
        $this->assertSame(2500.0, (float) $updated['paid_amount']);
        $this->assertSame(2000.0, (float) $updated['balance_amount']);
        $this->assertSame('partial', $updated['payment_status']);

        $payment = $updated['payments'][1];
        $this->get(route('admin.admissions.payments.receipt', [$application['id'], $payment['id']]))
            ->assertOk()->assertSee('INSTALLMENT RECEIPT')->assertSee('UPI-12345')->assertSee('1,000.00');

        $this->post(route('admin.admissions.payments.store', $application['id']), [
            'amount' => 2500, 'payment_date' => '2026-08-22', 'mode' => 'cash',
        ])->assertSessionHasErrors('amount');
        $this->assertSame(2500.0, (float) AdmissionStore::find($application['id'])['paid_amount']);
    }

}

<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseFeeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_or_edit_a_course_fee(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $course = Course::create([
            'code' => 'DCA',
            'title' => 'Diploma in Computer Applications',
            'duration' => '6 Months',
            'level' => 'Foundation',
            'summary' => 'Office skills',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'code' => 'DCA',
            'title' => 'Diploma in Computer Applications',
            'title_hi' => 'कंप्यूटर एप्लीकेशन डिप्लोमा',
            'duration' => '6 Months',
            'fee_amount' => '4500.00',
            'fee_note' => 'Registration included',
            'level' => 'Foundation',
            'summary' => 'Office skills',
            'eligibility' => '10th pass',
            'modules_text' => "MS Office\nInternet",
            'careers_text' => "Computer Operator\nOffice Assistant",
            'sort_order' => 1,
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'fee_amount' => 4500.00,
            'fee_note' => 'Registration included',
        ]);

        $this->get('/')->assertOk()->assertSee('4500', false)->assertSee('Registration included', false);
    }
}

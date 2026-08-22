<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Support\PracticeTestStore;
use App\Support\StarterPracticeTests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StarterPracticeTestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        @unlink(storage_path('app/cnet-practice-tests.json'));
        @unlink(storage_path('app/cnet-practice-attempts.json'));
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('app/cnet-practice-tests.json'));
        @unlink(storage_path('app/cnet-practice-attempts.json'));
        parent::tearDown();
    }

    public function test_starter_library_covers_every_center_course_with_valid_questions(): void
    {
        $expected=['DCA','ADCA','CCC','TALLY','EXCEL','DTP','WEB','PYTHON','DIGITAL','HARDWARE','AI','DATA'];
        $sets=StarterPracticeTests::all();
        $this->assertSame($expected,array_column($sets,'course_code'));
        foreach($sets as $set){
            $this->assertCount(5,$set['questions']);
            $this->assertSame(15,$set['duration_minutes']);
            $this->assertSame(40,$set['pass_percentage']);
            foreach($set['questions'] as $question){
                $this->assertCount(4,$question['options']);
                $this->assertArrayHasKey($question['correct'],$question['options']);
            }
        }
    }

    public function test_admin_page_installs_sets_for_active_courses_once_only(): void
    {
        Course::create(['code'=>'DCA','title'=>'DCA','duration'=>'6 Months','fee_amount'=>6000,'level'=>'Foundation','summary'=>'Course','is_active'=>true]);
        Course::create(['code'=>'TALLY','title'=>'Tally','duration'=>'3 Months','fee_amount'=>6000,'level'=>'Career','summary'=>'Course','is_active'=>true]);
        Course::create(['code'=>'WEB','title'=>'Web','duration'=>'6 Months','fee_amount'=>6000,'level'=>'Technical','summary'=>'Course','is_active'=>false]);
        $admin=User::factory()->create(['is_admin'=>true]);

        $this->actingAs($admin)->get(route('admin.practice.index'))
            ->assertOk()->assertSee('2 ready-made course test sets installed automatically.')
            ->assertSee('DCA Computer Fundamentals')->assertSee('Tally Prime &amp; GST',false);
        $this->assertCount(2,PracticeTestStore::all());

        $this->actingAs($admin)->get(route('admin.practice.index'))->assertOk()
            ->assertDontSee('installed automatically');
        $this->assertCount(2,PracticeTestStore::all());
    }
}

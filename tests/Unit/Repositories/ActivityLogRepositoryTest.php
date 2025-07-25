<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ActivityLog\ActivityLogRepository;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityLogRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $user;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ActivityLogRepository();
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_can_create_activity_log()
    {
        $logData = [
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
            'details' => 'User completed task',
        ];

        $log = $this->repository->create($logData);

        $this->assertInstanceOf(ActivityLog::class, $log);
        $this->assertEquals($this->user->id, $log->user_id);
        $this->assertEquals($this->task->id, $log->task_id);
        $this->assertEquals('User completed task', $log->details);
    }

    public function test_can_get_all_logs()
    {
        ActivityLog::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
        ]);

        $logs = $this->repository->all();

        $this->assertCount(3, $logs);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $logs);
    }

    public function test_can_get_logs_by_user()
    {
        $otherUser = User::factory()->create();
        $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

        ActivityLog::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
        ]);

        ActivityLog::factory()->create([
            'user_id' => $otherUser->id,
            'task_id' => $otherTask->id,
        ]);

        $logs = $this->repository->byUser($this->user->id);

        $this->assertCount(2, $logs);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $logs);
    }

    public function test_can_count_logs()
    {
        ActivityLog::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
        ]);

        $count = $this->repository->count();

        $this->assertEquals(5, $count);
    }

    public function test_logs_are_ordered_by_latest()
    {
        $oldLog = ActivityLog::factory()->create([
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
            'created_at' => now()->subDays(1),
        ]);

        $newLog = ActivityLog::factory()->create([
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
            'created_at' => now(),
        ]);

        $logs = $this->repository->all();

        $this->assertEquals($newLog->id, $logs->first()->id);
        $this->assertEquals($oldLog->id, $logs->last()->id);
    }

    public function test_logs_include_user_relationship()
    {
        ActivityLog::factory()->create([
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
        ]);

        $logs = $this->repository->all();

        $this->assertTrue($logs->first()->relationLoaded('user'));
        $this->assertEquals($this->user->id, $logs->first()->user->id);
    }

    public function test_logs_include_task_relationship()
    {
        ActivityLog::factory()->create([
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
        ]);

        $logs = $this->repository->all();

        $this->assertTrue($logs->first()->relationLoaded('task'));
        $this->assertEquals($this->task->id, $logs->first()->task->id);
    }
} 
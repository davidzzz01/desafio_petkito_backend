<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\Task\TaskRepository;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TaskRepository();
        $this->user = User::factory()->create();
    }

    public function test_can_create_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => '2025-12-31',
            'completed' => false,
            'user_id' => $this->user->id,
        ];

        $task = $this->repository->create($taskData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals($this->user->id, $task->user_id);
    }

    public function test_can_find_task_by_id()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $foundTask = $this->repository->find($task->id);

        $this->assertInstanceOf(Task::class, $foundTask);
        $this->assertEquals($task->id, $foundTask->id);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = ['title' => 'Updated Task'];

        $updatedTask = $this->repository->update($task->id, $updateData);

        $this->assertEquals('Updated Task', $updatedTask->title);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $result = $this->repository->delete($task->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_can_get_tasks_for_user()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);
        Task::factory()->create(['user_id' => User::factory()->create()->id]);

        $tasks = $this->repository->allForUser($this->user->id);

        $this->assertCount(3, $tasks);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $tasks);
    }

    public function test_can_search_tasks_by_title()
    {
        Task::factory()->create([
            'title' => 'Meeting with client',
            'user_id' => $this->user->id
        ]);
        Task::factory()->create([
            'title' => 'Send email',
            'user_id' => $this->user->id
        ]);

        $results = $this->repository->search('meeting');

        $this->assertCount(1, $results);
        $this->assertEquals('Meeting with client', $results->first()->title);
    }

    public function test_can_search_tasks_by_description()
    {
        Task::factory()->create([
            'title' => 'Task 1',
            'description' => 'Important meeting with client',
            'user_id' => $this->user->id
        ]);
        Task::factory()->create([
            'title' => 'Task 2',
            'description' => 'Send email to team',
            'user_id' => $this->user->id
        ]);

        $results = $this->repository->search('meeting');

        $this->assertCount(1, $results);
        $this->assertEquals('Task 1', $results->first()->title);
    }

    public function test_can_get_tasks_by_status()
    {
        Task::factory()->create(['completed' => true, 'user_id' => $this->user->id]);
        Task::factory()->create(['completed' => false, 'user_id' => $this->user->id]);

        $completedTasks = $this->repository->getByStatus(true);
        $pendingTasks = $this->repository->getByStatus(false);

        $this->assertCount(1, $completedTasks);
        $this->assertCount(1, $pendingTasks);
    }

    public function test_can_count_tasks()
    {
        Task::factory()->count(5)->create(['user_id' => $this->user->id]);

        $count = $this->repository->count();

        $this->assertEquals(5, $count);
    }
} 
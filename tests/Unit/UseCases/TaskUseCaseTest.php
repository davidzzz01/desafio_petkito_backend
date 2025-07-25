<?php

namespace Tests\Unit\UseCases;

use Tests\TestCase;
use App\UseCases\Task\TaskUseCase;
use App\Repositories\Task\TaskRepository;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class TaskUseCaseTest extends TestCase
{
    use RefreshDatabase;

    protected $useCase;
    protected $repository;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TaskRepository::class);
        $this->useCase = new TaskUseCase($this->repository);
        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_list_tasks_for_user()
    {
        $tasks = collect([
            Task::factory()->make(['user_id' => $this->user->id]),
            Task::factory()->make(['user_id' => $this->user->id]),
        ]);

        $this->repository->shouldReceive('allForUser')
            ->with($this->user->id)
            ->once()
            ->andReturn($tasks);

        $result = $this->useCase->list($this->user->id);

        $this->assertEquals($tasks, $result);
    }

    public function test_can_show_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->repository->shouldReceive('find')
            ->with($task->id)
            ->once()
            ->andReturn($task);

        $result = $this->useCase->show($task->id);

        $this->assertEquals($task, $result);
    }

    public function test_can_create_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => '2025-12-31',
            'user_id' => $this->user->id,
        ];

        $task = Task::factory()->make($taskData);

        $this->repository->shouldReceive('create')
            ->with($taskData)
            ->once()
            ->andReturn($task);

        $result = $this->useCase->create($taskData);

        $this->assertEquals($task, $result);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = ['title' => 'Updated Task'];

        $this->repository->shouldReceive('update')
            ->with($task->id, $updateData)
            ->once()
            ->andReturn($task->fresh());

        $result = $this->useCase->update($task->id, $updateData);

        $this->assertEquals($task->fresh(), $result);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->repository->shouldReceive('delete')
            ->with($task->id)
            ->once()
            ->andReturn(true);

        $result = $this->useCase->delete($task->id);

        $this->assertTrue($result);
    }

    public function test_can_complete_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false
        ]);

        $this->repository->shouldReceive('find')
            ->with($task->id)
            ->once()
            ->andReturn($task);

        $this->repository->shouldReceive('update')
            ->with($task->id, ['completed' => true])
            ->once()
            ->andReturn($task->fresh());

        $result = $this->useCase->complete($task->id);

        $this->assertTrue($result->completed);
    }

    public function test_can_get_tasks_by_status()
    {
        $tasks = collect([
            Task::factory()->make(['completed' => true]),
        ]);

        $this->repository->shouldReceive('getByStatus')
            ->with(true)
            ->once()
            ->andReturn($tasks);

        $result = $this->useCase->getByStatus(true);

        $this->assertEquals($tasks, $result);
    }

    public function test_can_search_tasks()
    {
        $tasks = collect([
            Task::factory()->make(['title' => 'Meeting task']),
        ]);

        $this->repository->shouldReceive('search')
            ->with('meeting')
            ->once()
            ->andReturn($tasks);

        $result = $this->useCase->search('meeting');

        $this->assertEquals($tasks, $result);
    }

    public function test_search_trims_input()
    {
        $tasks = collect([
            Task::factory()->make(['title' => 'Meeting task']),
        ]);

        $this->repository->shouldReceive('search')
            ->with('meeting')
            ->once()
            ->andReturn($tasks);

        $result = $this->useCase->search('  meeting  ');

        $this->assertEquals($tasks, $result);
    }

    public function test_search_returns_empty_for_short_input()
    {
        $result = $this->useCase->search('ab');

        $this->assertTrue($result->isEmpty());
    }

    public function test_can_get_all_with_pagination()
    {
        $tasks = collect([
            Task::factory()->make(['user_id' => $this->user->id]),
        ]);

        $this->repository->shouldReceive('count')
            ->once()
            ->andReturn(1);

        $this->repository->shouldReceive('allForUser')
            ->with($this->user->id)
            ->once()
            ->andReturn($tasks);

        $result = $this->useCase->getAll(10, 1);

        $this->assertEquals([
            'total' => 1,
            'per_page' => 10,
            'page' => 1,
            'data' => $tasks,
        ], $result);
    }
} 
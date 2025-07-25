<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_tasks_with_pagination()
    {
        Sanctum::actingAs($this->user);
        
        Task::factory()->count(15)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/tasks?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total',
                'per_page',
                'page',
                'next_page',
                'last_page',
                'previous_page',
                'data'
            ])
            ->assertJson([
                'total' => 15,
                'per_page' => 10,
                'page' => 1,
                'next_page' => 2,
                'last_page' => 2,
                'previous_page' => null,
            ]);
    }

    public function test_can_create_task()
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'due_date' => '2025-12-31',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'New Task',
                'description' => 'Task description',
                'user_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_show_task()
    {
        Sanctum::actingAs($this->user);
        
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
                'title' => $task->title,
            ]);
    }

    public function test_can_update_task()
    {
        Sanctum::actingAs($this->user);
        
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Task',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Task',
                'description' => 'Updated description',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
        ]);
    }

    public function test_can_delete_task()
    {
        Sanctum::actingAs($this->user);
        
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Deleted']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_can_complete_task()
    {
        Sanctum::actingAs($this->user);
        
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}/complete");

        $response->assertStatus(200)
            ->assertJson(['completed' => true]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => true,
        ]);
    }

    public function test_can_search_tasks()
    {
        Sanctum::actingAs($this->user);
        
        Task::factory()->create([
            'title' => 'Meeting with client',
            'user_id' => $this->user->id
        ]);
        Task::factory()->create([
            'title' => 'Send email',
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/tasks/search?q=meeting');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'Meeting with client']);
    }

    public function test_search_returns_404_when_no_results()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/tasks/search?q=nonexistent');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Nenhuma tarefa encontrada.']);
    }

    public function test_can_get_tasks_by_status()
    {
        Sanctum::actingAs($this->user);
        
        Task::factory()->create([
            'completed' => true,
            'user_id' => $this->user->id
        ]);
        Task::factory()->create([
            'completed' => false,
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/tasks/status?completed=true');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }

    public function test_user_cannot_access_other_user_task()
    {
        Sanctum::actingAs($this->user);
        
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(404);
    }
} 
<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\User\UserRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    public function test_can_get_all_users()
    {
        User::factory()->count(3)->create();

        $users = $this->repository->all();

        $this->assertCount(3, $users);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $users);
    }

    public function test_can_find_user_by_id()
    {
        $user = User::factory()->create();

        $foundUser = $this->repository->find($user->id);

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    public function test_can_create_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ];

        $user = $this->repository->create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create();
        $updateData = ['name' => 'Updated Name'];

        $updatedUser = $this->repository->update($user->id, $updateData);

        $this->assertEquals('Updated Name', $updatedUser->name);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $result = $this->repository->delete($user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_can_find_user_by_email()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $foundUser = $this->repository->findByEmail('test@example.com');

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('test@example.com', $foundUser->email);
    }

    public function test_find_by_email_returns_null_when_not_found()
    {
        $foundUser = $this->repository->findByEmail('nonexistent@example.com');

        $this->assertNull($foundUser);
    }

    public function test_can_count_users()
    {
        User::factory()->count(5)->create();

        $count = $this->repository->count();

        $this->assertEquals(5, $count);
    }
} 
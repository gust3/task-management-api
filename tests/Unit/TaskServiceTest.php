<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TaskService;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskService();
    }

    #[Test]
    public function can_create_task(): void
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'Description',
            'status' => 'pending',
        ];

        $task = $this->service->createTask($data);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertDatabaseHas('tasks', $data);
    }

    #[Test]
    public function can_get_task_by_id(): void
    {
        $task = Task::factory()->create(['title' => 'Test']);

        $foundTask = $this->service->getTaskById($task->id);

        $this->assertEquals($task->id, $foundTask->id);
        $this->assertEquals('Test', $foundTask->title);
    }

    #[Test]
    public function throws_exception_when_task_not_found(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->getTaskById(999);
    }

    #[Test]
    public function can_update_task(): void
    {
        $task = Task::factory()->create(['title' => 'Old']);

        $updatedTask = $this->service->updateTask($task->id, ['title' => 'New']);

        $this->assertEquals('New', $updatedTask->title);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New']);
    }

    #[Test]
    public function can_delete_task(): void
    {
        $task = Task::factory()->create();

        $this->service->deleteTask($task->id);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function can_get_all_task_ids(): void
    {
        Task::factory()->create(['id' => 1]);
        Task::factory()->create(['id' => 2]);
        Task::factory()->create(['id' => 3]);

        $ids = $this->service->getAllTaskIds();

        $this->assertCount(3, $ids);
        $this->assertContains(1, $ids);
        $this->assertContains(2, $ids);
        $this->assertContains(3, $ids);
    }
}
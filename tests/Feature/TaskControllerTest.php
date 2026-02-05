<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Устанавливаем локаль из конфигурации приложения
        $locale = config('app.locale', 'ru');
        config(['app.locale' => $locale]);
        App::setLocale($locale);
    }

    #[Test]
    public function can_get_all_tasks(): void
    {
        // ARRANGE: Создаем тестовые задачи в базе
        $task1 = Task::factory()->create(['title' => 'Task 1']);
        $task2 = Task::factory()->create(['title' => 'Task 2']);

        // ACT: Отправляем GET запрос
        $response = $this->withHeaders(['Accept-Language' => config('app.locale', 'ru')])
            ->getJson('/api/tasks');

        // ASSERT: Проверяем ответ
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('messages.success.task_list_retrieved'),
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['title' => 'Task 1'])
            ->assertJsonFragment(['title' => 'Task 2']);
    }

    #[Test]
    public function can_create_task(): void
    {
        // ARRANGE: Подготавливаем данные для создания
        $data = [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
        ];

        // ACT: Отправляем POST запрос
        $response = $this->withHeaders(['Accept-Language' => config('app.locale', 'ru')])
            ->postJson('/api/tasks', $data);

        // ASSERT: Проверяем ответ и базу данных
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => __('messages.success.task_created'),
            ])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('tasks', $data);
    }

    #[Test]
    public function validation_error_when_creating_task_without_title(): void
    {
        // ARRANGE: Готовим данные БЕЗ обязательного поля 'title'
        $data = [
            'description' => 'Without title',
        ];

        // ACT: Отправляем POST запрос
        $response = $this->withHeaders(['Accept-Language' => config('app.locale', 'ru')])
            ->postJson('/api/tasks', $data);

        // ASSERT: Проверяем ошибку валидации
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => __('messages.error.validation_failed'),
            ])
            ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function can_get_single_task(): void
    {
        // ARRANGE: Создаем задачу в базе
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'description' => 'Description',
            'status' => 'pending',
        ]);

        // ACT: Отправляем GET запрос для конкретной задачи
        $response = $this->withHeaders(['Accept-Language' => config('app.locale', 'ru')])
            ->getJson("/api/tasks/{$task->id}");

        // ASSERT: Проверяем ответ
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('messages.success.task_retrieved'),
            ])
            ->assertJsonFragment([
                'id' => $task->id,
                'title' => 'Test Task',
                'description' => 'Description',
                'status' => 'pending',
            ]);
    }

    #[Test]
    public function returns_404_for_nonexistent_task(): void
    {
        // ARRANGE: Нет подготовки — запрашиваем несуществующую задачу

        // ACT: Отправляем GET запрос для несуществующего ID
        $response = $this->withHeaders(['Accept-Language' => config('app.locale', 'ru')])
            ->getJson('/api/tasks/999');

        // ASSERT: Проверяем ошибку 404
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => __('messages.error.task_not_found'),
            ])
            ->assertJsonStructure(['available_ids']);
    }

    #[Test]
    public function can_update_task(): void
    {
        // ARRANGE: Создаем задачу с начальными данными
        $task = Task::factory()->create([
            'title' => 'Old Title',
            'status' => 'pending',
        ]);

        // ARRANGE: Подготавливаем новые данные
        $data = [
            'title' => 'New Title',
            'status' => 'completed',
        ];

        // ACT: Отправляем PUT запрос
        $response = $this->withHeaders(['Accept-Language' => config('app.locale', 'ru')])
            ->putJson("/api/tasks/{$task->id}", $data);

        // ASSERT: Проверяем ответ и базу
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('messages.success.task_updated'),
            ])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'New Title',
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function can_delete_task(): void
    {
        // ARRANGE: Создаем задачу
        $task = Task::factory()->create();

        // ACT: Отправляем DELETE запрос
        $response = $this->withHeaders(['Accept-Language' => config('app.locale', 'ru')])
            ->deleteJson("/api/tasks/{$task->id}");

        // ASSERT: Проверяем ответ и базу
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('messages.success.task_deleted'),
            ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function returns_404_when_deleting_nonexistent_task(): void
    {
        // ARRANGE: Нет подготовки — удаляем несуществующую задачу

        // ACT: Отправляем DELETE запрос для несуществующего ID
        $response = $this->withHeaders(['Accept-Language' => config('app.locale', 'ru')])
            ->deleteJson('/api/tasks/999');

        // ASSERT: Проверяем ошибку 404
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => __('messages.error.task_not_found'),
            ]);
    }

    // Дополнительный тест: Проверка переключения локали
    #[Test]
    public function can_get_tasks_in_different_languages(): void
    {
        // ARRANGE: Создаем задачу
        $task = Task::factory()->create(['title' => 'Test Task']);

        // ACT & ASSERT: Английский язык
        $response = $this->withHeaders(['Accept-Language' => 'en'])
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task retrieved successfully',
            ]);

        // ACT & ASSERT: Русский язык
        $response = $this->withHeaders(['Accept-Language' => 'ru'])
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Задача успешно получена',
            ]);
    }
}

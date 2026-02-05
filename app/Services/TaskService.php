<?php

namespace App\Services;

use App\Models\Task;
use App\Enums\TaskStatus; // ← Добавь этот импорт
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    /**
     * Получить все задачи
     */
    public function getAllTasks()
    {
        return Task::all();
    }

    /**
     * Получить задачу по ID
     *
     * @throws ModelNotFoundException
     */
    public function getTaskById(int $id): Task
    {
        $task = Task::find($id);
        if (!$task) {
            throw new ModelNotFoundException("Задача с ID {$id} не найдена");
        }
        return $task;
    }

    /**
     * Создать новую задачу
     */
    public function createTask(array $data): Task
    {
        // Валидация статуса через enum
        if (isset($data['status'])) {
            $data['status'] = TaskStatus::from($data['status'])->value;
        }

        return Task::create($data);
    }

    /**
     * Обновить задачу
     *
     * @throws ModelNotFoundException
     */
    public function updateTask(int $id, array $data): Task
    {
        $task = $this->getTaskById($id);

        // Валидация статуса через enum
        if (isset($data['status'])) {
            $data['status'] = TaskStatus::from($data['status'])->value;
        }

        $task->update($data);
        return $task;
    }

    /**
     * Удалить задачу
     *
     * @throws ModelNotFoundException
     */
    public function deleteTask(int $id): int
    {
        $task = $this->getTaskById($id);
        return $task->delete();
    }

    /**
     * Получить все доступные ID задач
     */
    public function getAllTaskIds(): array
    {
        return Task::pluck('id')->toArray();
    }

    /**
     * Получить задачи по статусу
     */
    public function getTasksByStatus(TaskStatus|string $status)
    {
        $statusValue = $status instanceof TaskStatus ? $status->value : $status;
        return Task::where('status', $statusValue)->get();
    }

    /**
     * Получить задачи в ожидании
     */
    public function getPendingTasks()
    {
        return Task::pending()->get();
    }

    /**
     * Получить задачи в работе
     */
    public function getInProgressTasks()
    {
        return Task::inProgress()->get();
    }

    /**
     * Получить завершённые задачи
     */
    public function getCompletedTasks()
    {
        return Task::completed()->get();
    }

    /**
     * Изменить статус задачи
     *
     * @throws ModelNotFoundException
     */
    public function changeStatus(int $id, TaskStatus|string $status): Task
    {
        $task = $this->getTaskById($id);
        $statusValue = $status instanceof TaskStatus ? $status->value : $status;
        $task->update(['status' => $statusValue]);
        return $task;
    }

    /**
     * Завершить задачу
     *
     * @throws ModelNotFoundException
     */
    public function completeTask(int $id): Task
    {
        return $this->changeStatus($id, TaskStatus::COMPLETED);
    }

    /**
     * Начать выполнение задачи
     *
     * @throws ModelNotFoundException
     */
    public function startTask(int $id): Task
    {
        return $this->changeStatus($id, TaskStatus::IN_PROGRESS);
    }

    /**
     * Вернуть задачу в ожидание
     *
     * @throws ModelNotFoundException
     */
    public function pauseTask(int $id): Task
    {
        return $this->changeStatus($id, TaskStatus::PENDING);
    }

    /**
     * Получить статистику по статусам
     */
    public function getStatusStatistics(): array
    {
        return [
            'pending' => Task::pending()->count(),
            'in_progress' => Task::inProgress()->count(),
            'completed' => Task::completed()->count(),
            'total' => Task::count(),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Task;
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
       if (! $task) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Задача с ID {$id} не найдена");
       }
       return $task;
    }

    /**
     * Создать новую задачу
     */
    public function createTask(array $data): Task
    {
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
}
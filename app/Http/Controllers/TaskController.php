<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[OA\Info(
    title: "To-Do List API",
    version: "1.0.0",
    description: "REST API для управления списком задач",
    contact: new OA\Contact(email: "your.email@example.com")
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Локальный сервер разработки"
)]
#[OA\Tag(
    name: "Tasks",
    description: "Операции с задачами (CRUD)"
)]
class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[OA\Get(
        path: "/tasks",
        summary: "Получить список всех задач",
        tags: ["Tasks"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tasks retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Task")
                        ),
                        new OA\Property(property: "count", type: "integer", example: 5)
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $tasks = $this->taskService->getAllTasks();

        return response()->json([
            'success' => true,
            'message' => __('messages.success.task_list_retrieved'),
            'data' => TaskResource::collection($tasks),
            'count' => $tasks->count(),
        ]);
    }

    #[OA\Post(
        path: "/tasks",
        summary: "Создать новую задачу",
        tags: ["Tasks"],
        requestBody: new OA\RequestBody(
        required: true,
        description: "Данные для создания задачи",
        content: new OA\JsonContent(ref: "#/components/schemas/TaskStore")
    ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Задача успешно создана",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Task created successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Task")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
                        new OA\Property(property: "errors", type: "object")
                    ]
                )
            )
        ]
    )]
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('messages.success.task_created'),
            'data' => new TaskResource($task),
        ], 201);
    }

    #[OA\Get(
        path: "/tasks/{id}",
        summary: "Получить задачу по ID",
        tags: ["Tasks"],
        parameters: [
        new OA\Parameter(
            name: "id",
            description: "ID задачи",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer", example: 1)
        )
    ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Task retrieved successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Task")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Задача не найдена",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Task not found")
                    ]
                )
            )
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $task = $this->taskService->getTaskById((int) $id);

        return response()->json([
            'success' => true,
            'message' => __('messages.success.task_retrieved'),
            'data' => new TaskResource($task),
        ]);
    }

    #[OA\Put(
        path: "/tasks/{id}",
        summary: "Обновить задачу",
        tags: ["Tasks"],
        parameters: [
        new OA\Parameter(
            name: "id",
            description: "ID задачи",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer", example: 1)
        )
    ],
        requestBody: new OA\RequestBody(
        required: true,
        description: "Данные для обновления задачи",
        content: new OA\JsonContent(ref: "#/components/schemas/TaskStore")
    ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Задача успешно обновлена",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Task updated successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Task")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Задача не найдена"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $task = $this->taskService->updateTask((int) $id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => __('messages.success.task_updated'),
            'data' => new TaskResource($task),
        ]);
    }

    #[OA\Delete(
        path: "/tasks/{id}",
        summary: "Удалить задачу",
        tags: ["Tasks"],
        parameters: [
        new OA\Parameter(
            name: "id",
            description: "ID задачи",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer", example: 1)
        )
    ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Задача успешно удалена",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Task deleted successfully"),
                        new OA\Property(property: "hint", type: "string", example: "Task with ID 1 has been deleted")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Задача не найдена"
            )
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $this->taskService->deleteTask((int) $id);

        return response()->json([
            'success' => true,
            'message' => __('messages.success.task_deleted'),
            'hint' => __('messages.hint.task_deleted', ['id' => $id]),
        ]);
    }
}

#[OA\Schema(
    schema: "Task",
    title: "Task",
    description: "Задача",
    required: ["id", "title", "status", "created_at", "updated_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", description: "Уникальный идентификатор задачи", example: 1),
        new OA\Property(property: "title", type: "string", description: "Заголовок задачи", example: "Завершить проект"),
        new OA\Property(property: "description", type: "string", description: "Описание задачи", example: "Необходимо завершить до конца недели", nullable: true),
        new OA\Property(property: "status", type: "string", description: "Статус задачи", enum: ["pending", "completed"], example: "pending"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", description: "Дата создания"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", description: "Дата последнего обновления")
    ]
)]
class TaskSchema {}

#[OA\Schema(
    schema: "TaskStore",
    title: "TaskStore",
    description: "Данные для создания/обновления задачи",
    required: ["title", "status"],
    properties: [
        new OA\Property(property: "title", type: "string", description: "Заголовок задачи", example: "Новая задача"),
        new OA\Property(property: "description", type: "string", description: "Описание задачи", example: "Описание новой задачи", nullable: true),
        new OA\Property(property: "status", type: "string", description: "Статус задачи", enum: ["pending", "completed"], example: "pending")
    ]
)]
class TaskStoreSchema {}

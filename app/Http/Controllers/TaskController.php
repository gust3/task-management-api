<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('messages.success.task_created'),
            'data' => new TaskResource($task),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $task = $this->taskService->getTaskById((int) $id);

        return response()->json([
            'success' => true,
            'message' => __('messages.success.task_retrieved'),
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $task = $this->taskService->updateTask((int) $id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => __('messages.success.task_updated'),
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
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
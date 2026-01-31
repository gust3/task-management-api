<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return response()->json([
        'name' => 'Task Management API',
        'version' => '1.0.0',
        'description' => 'Simple REST API for managing tasks',
        'endpoints' => [
            'GET /api/tasks' => 'Get all tasks',
            'POST /api/tasks' => 'Create a new task',
            'GET /api/tasks/{id}' => 'Get a single task',
            'PUT /api/tasks/{id}' => 'Update a task',
            'DELETE /api/tasks/{id}' => 'Delete a task',
        ],
        'status_values' => [
            'pending' => 'Task is pending',
            'in_progress' => 'Task is in progress',
            'completed' => 'Task is completed',
        ],
    ]);
});

Route::apiResource('tasks', TaskController::class);
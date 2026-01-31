<?php

return [
    // Success
    'success' => [
        'task_list_retrieved' => 'Task list retrieved successfully',
        'task_created' => 'Task created successfully',
        'task_retrieved' => 'Task retrieved successfully',
        'task_updated' => 'Task updated successfully',
        'task_deleted' => 'Task deleted successfully',
    ],

    // Errors
    'error' => [
        'task_not_found' => 'Task not found',
        'task_not_found_hint' => 'Check the ID. The task may have been deleted.',
        'resource_not_found' => 'Resource not found',
        'resource_not_found_hint' => 'Check the ID',
        'validation_failed' => 'Validation failed',
        'validation_hint' => 'Check the input fields',
        'internal_error' => 'Internal server error',
        'internal_error_hint' => 'Please try again later',
    ],

    // Hints
    'hint' => [
        'task_deleted' => 'Task with ID :id no longer exists in the database',
        'check_id' => 'Check the ID',
        'check_url' => 'Check the URL. You may have made a mistake in the address.',
    ],

    // Validation rules
    'validation_rules' => [
        'title' => 'Required field, string, maximum 255 characters',
        'description' => 'Optional field, string',
        'status' => 'Optional field, one of: ' . implode(', ', \App\Enums\TaskStatus::all()),
    ],
];
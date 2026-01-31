<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'setlocale' => \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->appendToGroup('api', [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Обработчик валидации
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error.validation_failed'),
                'hint' => __('messages.error.validation_hint'),
                'errors' => $e->errors(),
                'validation_rules' => [
                    'title' => __('messages.validation_rules.title'),
                    'description' => __('messages.validation_rules.description'),
                    'status' => __('messages.validation_rules.status'),
                ],
            ], 422);
        });

        // Обработчик 404 ошибок
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            $path = $request->path();

            if (str_starts_with($path, 'api/tasks')) {
                $segments = explode('/', $path);
                $segmentCount = count($segments);

                if ($segmentCount === 3) {
                    $taskId = $segments[2];

                    if (is_numeric($taskId)) {
                        try {
                            $availableIds = app(\App\Services\TaskService::class)->getAllTaskIds();
                        } catch (\Throwable $ex) {
                            $availableIds = [];
                        }

                        return response()->json([
                            'success' => false,
                            'message' => __('messages.error.task_not_found'),
                            'hint' => __('messages.error.task_not_found_hint'),
                            'available_ids' => $availableIds,
                        ], 404);
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => __('messages.error.resource_not_found'),
                'hint' => __('messages.error.resource_not_found_hint'),
            ], 404);
        });
    })->create();
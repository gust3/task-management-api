<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TaskStatus;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:' . TaskStatus::validationRule(),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Поле "title" обязательно для заполнения',
            'title.string' => 'Поле "title" должно быть строкой',
            'title.max' => 'Поле "title" не должно превышать 255 символов',
            'status.required' => 'Поле "status" обязательно для заполнения',
            'status.in' => 'Поле "status" должно быть одним из: ' . TaskStatus::validationRule(),
        ];
    }
}

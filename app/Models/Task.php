<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatus;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TaskStatus::class,
    ];

    /**
     * Скоуп для фильтрации по статусу
     */
    public function scopeStatus($query, TaskStatus|string $status)
    {
        $value = $status instanceof TaskStatus ? $status->value : $status;
        return $query->where('status', $value);
    }

    /**
     * Скоуп для задач в ожидании
     */
    public function scopePending($query)
    {
        return $query->where('status', TaskStatus::PENDING->value);
    }

    /**
     * Скоуп для задач в работе
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', TaskStatus::IN_PROGRESS->value);
    }

    /**
     * Скоуп для завершённых задач
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', TaskStatus::COMPLETED->value);
    }

    /**
     * Проверить, завершена ли задача
     */
    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::COMPLETED->value;
    }

    /**
     * Проверить, в процессе ли задача
     */
    public function isInProgress(): bool
    {
        return $this->status === TaskStatus::IN_PROGRESS->value;
    }

    /**
     * Проверить, в ожидании ли задача
     */
    public function isPending(): bool
    {
        return $this->status === TaskStatus::PENDING->value;
    }

    /**
     * Получить описание статуса
     */
    public function getStatusDescriptionAttribute(): string
    {
        return TaskStatus::from($this->status)->description();
    }
}

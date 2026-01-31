<?php


namespace App\Enums;

enum TaskStatus: string
{
case
    PENDING = 'pending';
case
    IN_PROGRESS = 'in_progress';
case
    COMPLETED = 'completed';

    /**
     * Получить описание статуса
     */
    public
    function description(): string
    {
        return match ($this) {
            self::PENDING => 'Задача в ожидании',
            self::IN_PROGRESS => 'Задача в работе',
            self::COMPLETED => 'Задача завершена',
        };
    }

    /**
     * Получить все статусы
     */
    public
    static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Получить все статусы с описаниями
     */
    public
    static function allWithDescriptions(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn(self $status) => $status->description(), self::cases())
        );
    }
}
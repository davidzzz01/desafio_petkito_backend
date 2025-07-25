<?php

namespace App\Observers;

use App\Models\Task;
use App\UseCases\ActivityLog\ActivityLogUseCase;

class ActivityLogObserver
{
    protected $useCase;

    public function __construct(ActivityLogUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function created(Task $task)
    {
        $user = auth()->user();
        if ($user) {
            $this->useCase->logConclusion(
                $user->id,
                $task->id,
                "{$user->name} criou a tarefa \"{$task->title}\""
            );
        }
    }

    public function updated(Task $task)
    {
        $user = auth()->user();
        if ($user && $task->wasChanged('completed') && $task->completed) {
            $this->useCase->logConclusion(
                $user->id,
                $task->id,
                "{$user->name} concluiu a tarefa \"{$task->title}\""
            );
        }
    }
} 
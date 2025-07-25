<?php

namespace App\UseCases\ActivityLog;

use App\Repositories\ActivityLog\ActivityLogRepository;

class ActivityLogUseCase
{
    protected $repository;

    public function __construct(ActivityLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function logConclusion($userId, $taskId, $details)
    {
        return $this->repository->create([
            'user_id' => $userId,
            'task_id' => $taskId,
            'details' => $details,
        ]);
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function byUser($userId)
    {
        return $this->repository->byUser($userId);
    }
} 
<?php

namespace App\Repositories\ActivityLog;

use App\Models\ActivityLog;

class ActivityLogRepository
{
    public function create(array $data)
    {
        return ActivityLog::create($data);
    }

    public function all()
    {
        return ActivityLog::with('user', 'task')->latest()->get();
    }

    public function byUser($userId)
    {
        return ActivityLog::where('user_id', $userId)->with('task')->latest()->get();
    }

    public function count()
    {
        return ActivityLog::count();
    }
} 
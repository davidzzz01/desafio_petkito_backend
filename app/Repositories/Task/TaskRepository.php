<?php

namespace App\Repositories\Task;
use Illuminate\Support\Facades\DB;
use App\Models\Task;

class TaskRepository
{
    public function allForUser($userId)
    {
        return Task::where('user_id', $userId)->get();
    }

    public function find($id)
    {
        return Task::findOrFail($id);
    }

    public function create(array $data)
    {
        return Task::create($data);
    }

    public function update($id, array $data)
    {
        $task = $this->find($id);
        $task->update($data);
        return $task;
    }

    public function delete($id)
    {
        $task = $this->find($id);
        return $task->delete();
    }

    public function getByStatus($completed)
    {
        return Task::where('completed', $completed)->get();
    }

public function search($word)
{
    return Task::where('title', 'like', '%' . $word . '%')
        ->orWhere('description', 'like', '%' . $word . '%')
        ->get();
}

public function count()
{
    return Task::count();
}


    public function allWithUser()
{
    return Task::with('user')->get();
}




}
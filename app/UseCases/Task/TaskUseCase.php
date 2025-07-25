<?php

namespace App\UseCases\Task;

use App\Repositories\Task\TaskRepository;

class TaskUseCase
{
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function list($userId)
    {
        return $this->taskRepository->allForUser($userId);
    }

    public function show($id)
    {
        return $this->taskRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->taskRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->taskRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->taskRepository->delete($id);
    }

    public function complete($id)
    {
        $task = $this->taskRepository->find($id);
        
        $completed = !$task->completed;
        
        return $this->taskRepository->update($id, ['completed' => $completed]);
    }

    public function getByStatus($completed)
    {
        return $this->taskRepository->getByStatus($completed);
    }

 public function search($word)
{
    $word = trim($word);

    if (empty($word) || strlen($word) < 3) {
        return collect(); 
    }

    return $this->taskRepository->search($word);
}




public function forReport($user)
{
    if ($user->is_admin) {
        return $this->taskRepository->allWithUser(); 
    }

    return $this->taskRepository->allForUser($user->id);
}

public function getAll($perPage, $page)
{
    $offset = ($page - 1) * $perPage;

    $total = $this->taskRepository->count();
    $tasks = $this->taskRepository->allForUser(auth()->id());

    return [
        'total' => $total,
        'per_page' => $perPage,
        'page' => $page,
        'data' => $tasks,
    ];
}


}

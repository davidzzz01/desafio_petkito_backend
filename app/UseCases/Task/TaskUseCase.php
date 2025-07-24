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
    // Busca a tarefa atual
    $task = $this->taskRepository->find($id);
    
    // Alterna o status (toggle)
    $completed = !$task->completed;
    
    // Atualiza no banco
    return $this->taskRepository->update($id, ['completed' => $completed]);
}
}

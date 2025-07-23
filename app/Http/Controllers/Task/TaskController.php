<?php

namespace App\Http\Controllers\Task;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\UseCases\Task\TaskUseCase;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskUseCase;

    public function __construct(TaskUseCase $taskUseCase)
    {
        $this->taskUseCase = $taskUseCase;
    }

    public function index(Request $request)
    {
        $tasks = $this->taskUseCase->list($request->user()->id);
        return response()->json($tasks);
    }

    public function store(TaskStoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $task = $this->taskUseCase->create($data);
        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = $this->taskUseCase->show($id);
        return response()->json($task);
    }

    public function update(TaskUpdateRequest $request, $id)
    {
        $task = $this->taskUseCase->update($id, $request->validated());
        return response()->json($task);
    }

    public function destroy($id)
    {
        $this->taskUseCase->delete($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function complete($id)
    {
        $task = $this->taskUseCase->complete($id);
        return response()->json($task);
    }
}

<?php

namespace App\Http\Controllers\Task;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\UseCases\Task\TaskUseCase;
use Illuminate\Http\Request;
use App\UseCases\ActivityLog\ActivityLogUseCase;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function complete($id, ActivityLogUseCase $logUseCase)
    {
        $task = $this->taskUseCase->complete($id);
        if ($task->completed) {
            $user = auth()->user();
            $logUseCase->logConclusion(
                $user->id,
                $task->id,
                "{$user->name} concluiu a tarefa \"{$task->title}\""
            );
        }
        return response()->json($task);
    }

    public function tasksByStatus(Request $request)
    {
        $completed = $request->query('completed', false);
        $tasks = $this->taskUseCase->getByStatus($completed);
        return response()->json($tasks);
    }

  public function searchTasks(Request $request)
{
    $word = $request->query('q', '');
    $tasks = $this->taskUseCase->search($word);

    if ($tasks->isEmpty()) {
        return response()->json(['message' => 'Nenhuma tarefa encontrada.'], 404);
    }

    return response()->json($tasks);
}




    public function exportPdf(Request $request)
    {
        $user = $request->user();

        $tasks = $this->taskUseCase->forReport($user);

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'Nenhuma tarefa encontrada para gerar o relatório.'
            ], 404);
        }

        $pdf = Pdf::loadView('tasks.report', ['tasks' => $tasks]);

        return $pdf->download('relatorio_tarefas.pdf');
    }
}

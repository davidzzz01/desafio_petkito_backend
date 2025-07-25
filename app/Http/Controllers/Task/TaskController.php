<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\UseCases\Task\TaskUseCase;
use Illuminate\Http\Request;
use App\Traits\PaginateTrait;

class TaskController extends Controller
{
    use PaginateTrait;

    protected $taskUseCase;

    public function __construct(TaskUseCase $taskUseCase)
    {
        $this->taskUseCase = $taskUseCase;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $tasks = $this->taskUseCase->getAll($perPage, $page);

        $response = $this->paginate(
            $tasks['total'],
            $perPage,
            $page,
            $tasks['data']
        );

        return response()->json($response);
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tasks.report', ['tasks' => $tasks]);

        return $pdf->download('relatorio_tarefas.pdf');
    }
}

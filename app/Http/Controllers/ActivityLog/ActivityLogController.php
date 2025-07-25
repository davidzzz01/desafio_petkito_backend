<?php

namespace App\Http\Controllers\ActivityLog;

use App\Http\Controllers\Controller;
use App\UseCases\ActivityLog\ActivityLogUseCase;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\PaginateTrait;

class ActivityLogController extends Controller
{
    use PaginateTrait;

    protected $useCase;

    public function __construct(ActivityLogUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $logs = $this->useCase->getAll($perPage, $page);

        $response = $this->paginate(
            $logs['total'],
            $perPage,
            $page,
            $logs['data']
        );

        return response()->json($response);
    }

    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $logs = $user->is_admin
            ? $this->useCase->all()
            : $this->useCase->byUser($user->id);

        $pdf = Pdf::loadView('logs.report', ['logs' => $logs]);
        return $pdf->download('relatorio_logs.pdf');
    }
} 
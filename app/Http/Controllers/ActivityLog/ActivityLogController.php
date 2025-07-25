<?php

namespace App\Http\Controllers\ActivityLog;

use App\Http\Controllers\Controller;
use App\UseCases\ActivityLog\ActivityLogUseCase;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityLogController extends Controller
{
    protected $useCase;

    public function __construct(ActivityLogUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $logs = $user->is_admin
            ? $this->useCase->all()
            : $this->useCase->byUser($user->id);
        return response()->json($logs);
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
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportFilterRequest;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function summary(ReportFilterRequest $request)
    {
        $data = $this->reportService->buildSummary(
            $request->input('start_date'),
            $request->input('end_date'),
        );

        return view('reports.summary', $data);
    }

    public function downloadPdf(ReportFilterRequest $request)
    {
        $data = $this->reportService->buildSummary(
            $request->input('start_date'),
            $request->input('end_date'),
        );

        $pdf = Pdf::loadView('reports.summary-pdf', $data);

        return $pdf->download("Laporan-Ringkasan-{$data['start']}-{$data['end']}.pdf");
    }
}

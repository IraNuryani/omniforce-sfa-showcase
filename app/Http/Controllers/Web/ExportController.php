<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\VisitLog;
use App\Services\DocumentExportService;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Download formatted Visit Logs Excel
     */
    public function exportVisitLogs(DocumentExportService $exportService)
    {
        $spreadsheet = $exportService->exportVisitLogsExcel();

        $fileName = 'OmniForce_Visit_Audit_' . date('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Print / Preview Complaint & PICA Audit Document
     */
    public function printComplaintReport($id)
    {
        $complaint = Complaint::with(['customer.area', 'user', 'pica'])->findOrFail($id);
        return view('demo.report_print', compact('complaint'));
    }

    /**
     * Download Complaint Report as a Word-compatible HTML file
     */
    public function exportWordComplaint($id)
    {
        $complaint = Complaint::with(['customer.area', 'user', 'pica'])->findOrFail($id);
        $html = view('demo.report_print', compact('complaint'))->render();

        $fileName = "CAPA_PICA_Audit_{$complaint->ticket_number}.doc";

        return response($html, 200, [
            'Content-Type'        => 'application/msword',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}

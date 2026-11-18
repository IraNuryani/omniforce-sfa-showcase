<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\VisitLog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocumentExportService
{
    /**
     * Build an executive Excel report of Visit Logs
     */
    public function exportVisitLogsExcel()
    {
        $logs = VisitLog::with(['user', 'customer.area'])->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Visit Audit Log');

        // Header Title
        $sheet->setCellValue('A1', 'OMNIFORCE SFA — FIELD VISIT AUDIT REPORT');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Generated on: ' . date('d F Y, H:i') . ' | Total Records: ' . $logs->count());
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Table Column Headers
        $headers = ['Doc Number', 'Sales Representative', 'Store / Customer', 'Area', 'Time In', 'Duration (Min)', 'Geofence Valid', 'Notes'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        foreach ($headers as $idx => $header) {
            $col = $cols[$idx];
            $sheet->setCellValue("{$col}4", $header);
            $sheet->getStyle("{$col}4")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle("{$col}4")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E3A8A');
            $sheet->getStyle("{$col}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $row = 5;
        foreach ($logs as $log) {
            $sheet->setCellValue("A{$row}", $log->doc_number);
            $sheet->setCellValue("B{$row}", $log->user ? $log->user->name : 'N/A');
            $sheet->setCellValue("C{$row}", $log->customer ? $log->customer->name : 'N/A');
            $sheet->setCellValue("D{$row}", ($log->customer && $log->customer->area) ? $log->customer->area->name : '-');
            $sheet->setCellValue("E{$row}", $log->time_in ? $log->time_in->format('Y-m-d H:i') : '-');
            $sheet->setCellValue("F{$row}", $log->duration_minutes);
            $sheet->setCellValue("G{$row}", $log->is_within_geofence ? 'YES (Valid)' : 'FLAGGED (Outside)');
            $sheet->setCellValue("H{$row}", $log->notes ?: '-');

            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Highlight flagged geofence
            if (!$log->is_within_geofence) {
                $sheet->getStyle("G{$row}")->getFont()->getColor()->setARGB('FFDC2626');
            }

            $row++;
        }

        // Auto-size columns
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}

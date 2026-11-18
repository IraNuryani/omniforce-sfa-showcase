<?php

use App\Http\Controllers\Web\DemoController;
use App\Http\Controllers\Web\ExportController;
use Illuminate\Support\Facades\Route;

// Redirect root to mobile demo
Route::get('/', [DemoController::class, 'mobile'])->name('demo.mobile');
Route::get('/demo/mobile', [DemoController::class, 'mobile'])->name('demo.mobile.view');
Route::get('/demo/admin', [DemoController::class, 'admin'])->name('demo.admin');
Route::get('/demo/docs', [DemoController::class, 'docs'])->name('demo.docs');

// Export & Document Printing Routes
Route::get('/export/visit-logs/excel', [ExportController::class, 'exportVisitLogs'])->name('export.visit_logs.excel');
Route::get('/export/complaints/{id}/print', [ExportController::class, 'printComplaintReport'])->name('export.complaint.print');
Route::get('/export/complaints/{id}/word', [ExportController::class, 'exportWordComplaint'])->name('export.complaint.word');

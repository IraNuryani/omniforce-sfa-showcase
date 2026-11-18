<?php

use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\VisitLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Visit Logs Ingestion & Sync Pipeline
    Route::post('/visit-logs', [VisitLogController::class, 'store'])->name('api.visit_logs.store');
    Route::get('/visit-logs', [VisitLogController::class, 'index'])->name('api.visit_logs.index');
    Route::get('/visit-logs/staging-queue', [VisitLogController::class, 'getStagingQueue'])->name('api.visit_logs.staging');
    Route::post('/visit-logs/sync', [VisitLogController::class, 'triggerSync'])->name('api.visit_logs.sync');
    Route::get('/visit-logs/analytics', [VisitLogController::class, 'getAnalytics'])->name('api.visit_logs.analytics');

    // Field Quality Complaints & PICA
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('api.complaints.index');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('api.complaints.store');
});

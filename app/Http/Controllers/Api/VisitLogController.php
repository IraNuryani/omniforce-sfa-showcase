<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\VisitLog;
use App\Models\VisitLogTemp;
use App\Traits\DocumentNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class VisitLogController extends Controller
{
    use DocumentNumber;

    /**
     * Resilient Ingestion: Accepts mobile check-ins and pushes directly to staging queue
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'customer_id'   => 'required|exists:customers,id',
            'latitude_in'   => 'nullable|numeric',
            'longitude_in'  => 'nullable|numeric',
            'notes'         => 'nullable|string',
            'selfie_image'  => 'nullable|string', // base64 or photo URL
        ]);

        $docNumber = self::generateSequenceNumber('VISIT');

        // Immediate persistence into staging table
        $tempLog = VisitLogTemp::create([
            'doc_number'    => $docNumber,
            'user_id'       => $validated['user_id'],
            'customer_id'   => $validated['customer_id'],
            'time_in'       => now(),
            'time_out'      => now()->addMinutes(rand(15, 45)),
            'latitude_in'   => $validated['latitude_in'] ?? null,
            'longitude_in'  => $validated['longitude_in'] ?? null,
            'selfie_path'   => $validated['selfie_image'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
            'notes'         => $validated['notes'] ?? 'Store visit completed normally.',
            'sync_attempts' => 0,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Visit record accepted and queued in staging table for resilient sync',
            'data'    => $tempLog,
        ], 201);
    }

    /**
     * Get live list of production visit logs
     */
    public function index(Request $request)
    {
        $logs = VisitLog::with(['user', 'customer.area'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return self::success($logs, true);
    }

    /**
     * Get live pending logs in the staging queue
     */
    public function getStagingQueue()
    {
        $queue = VisitLogTemp::with(['user', 'customer'])->latest()->get();
        return self::crudSuccess($queue, 'Staging queue retrieved');
    }

    /**
     * Trigger background synchronization manually (for demo/testing)
     */
    public function triggerSync()
    {
        Artisan::call('visitlogs:sync');
        $output = Artisan::output();

        return response()->json([
            'status'  => true,
            'message' => 'Sync command executed successfully',
            'output'  => trim($output),
        ]);
    }

    /**
     * Real-time analytical metrics
     */
    public function getAnalytics()
    {
        $totalVisits = VisitLog::count();
        $validGeofence = VisitLog::where('is_within_geofence', true)->count();
        $complianceRate = $totalVisits > 0 ? round(($validGeofence / $totalVisits) * 100, 1) : 100;
        $pendingSync = VisitLogTemp::count();

        return response()->json([
            'status' => true,
            'data'   => [
                'total_visits'          => $totalVisits,
                'geofence_valid_visits' => $validGeofence,
                'compliance_rate'       => $complianceRate,
                'pending_staging_sync'  => $pendingSync,
            ],
        ]);
    }
}

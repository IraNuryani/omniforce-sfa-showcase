<?php

namespace App\Console\Commands;

use App\Models\VisitLog;
use App\Models\VisitLogTemp;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowcaseSyncCommand extends Command
{
    protected $signature = 'visitlogs:sync';
    protected $description = 'Synchronize pending visit logs from staging table (visit_logs_temp) to production';

    public function handle(): int
    {
        $this->info('🚀 Starting resilient visit log synchronization...');

        $pendingLogs = VisitLogTemp::where('sync_attempts', '<', 20)
            ->orderBy('sync_attempts')
            ->orderBy('created_at')
            ->limit(50)
            ->get();

        if ($pendingLogs->isEmpty()) {
            $this->info('ℹ️ Staging queue is empty. No logs to sync.');
            return 0;
        }

        $syncedCount = 0;
        $failedCount = 0;

        foreach ($pendingLogs as $temp) {
            try {
                DB::beginTransaction();

                if (VisitLog::where('doc_number', $temp->doc_number)->exists()) {
                    $temp->delete();
                    DB::commit();
                    continue;
                }

                $duration = rand(20, 40);

                // Haversine geofence calculation
                $isWithinGeofence = true;
                if ($temp->customer && $temp->customer->latitude && $temp->latitude_in) {
                    $distance = $this->calculateDistanceMeters(
                        $temp->latitude_in,
                        $temp->longitude_in,
                        $temp->customer->latitude,
                        $temp->customer->longitude
                    );
                    $isWithinGeofence = ($distance <= 250);
                }

                VisitLog::create([
                    'doc_number'         => $temp->doc_number ?: 'VISIT-' . date('Ymd') . '-' . rand(1000, 9999),
                    'user_id'            => $temp->user_id,
                    'customer_id'        => $temp->customer_id,
                    'time_in'            => $temp->time_in ?: now(),
                    'time_out'           => $temp->time_out ?: now()->addMinutes($duration),
                    'duration_minutes'   => $duration,
                    'latitude_in'        => $temp->latitude_in,
                    'longitude_in'       => $temp->longitude_in,
                    'latitude_out'       => $temp->latitude_out,
                    'longitude_out'      => $temp->longitude_out,
                    'selfie_path'        => $temp->selfie_path,
                    'image_path'         => $temp->image_path,
                    'notes'              => $temp->notes,
                    'is_within_geofence' => $isWithinGeofence,
                ]);

                $temp->delete();
                DB::commit();

                $syncedCount++;
                $this->info("✅ Synced visit {$temp->doc_number}");
            } catch (Exception $e) {
                DB::rollBack();
                $temp->increment('sync_attempts');
                $temp->update(['last_error' => $e->getMessage()]);
                $failedCount++;
                $this->error("❌ Failed to sync: {$e->getMessage()}");
            }
        }

        $this->info("🎉 Finished. Successfully synced: {$syncedCount}, Failed: {$failedCount}");
        return 0;
    }

    private function calculateDistanceMeters($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}

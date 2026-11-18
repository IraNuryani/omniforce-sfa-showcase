<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use App\Models\VisitLog;
use App\Models\VisitLogTemp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class VisitLogStagingTest extends TestCase
{
    /**
     * Test mobile visit submission writes directly to the staging queue
     */
    public function test_visit_check_in_writes_to_staging_table_immediately(): void
    {
        $user = User::where('role', 'sales_rep')->first();
        $customer = Customer::first();

        $response = $this->postJson('/api/v1/visit-logs', [
            'user_id'      => $user->id,
            'customer_id'  => $customer->id,
            'latitude_in'  => $customer->latitude,
            'longitude_in' => $customer->longitude,
            'notes'        => 'Test automated visit insertion.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
            ]);

        $this->assertDatabaseHas('visit_logs_temp', [
            'user_id'     => $user->id,
            'customer_id' => $customer->id,
        ]);
    }

    /**
     * Test the background sync command migrates staging rows to production
     */
    public function test_sync_command_transfers_staging_to_production(): void
    {
        $user = User::where('role', 'sales_rep')->first();
        $customer = Customer::first();

        $temp = VisitLogTemp::create([
            'doc_number'    => 'VISIT-UNIT-TEST-' . rand(1000, 9999),
            'user_id'       => $user->id,
            'customer_id'   => $customer->id,
            'latitude_in'   => $customer->latitude,
            'longitude_in'  => $customer->longitude,
            'notes'         => 'Pending staging test item.',
            'sync_attempts' => 0,
        ]);

        Artisan::call('visitlogs:sync');

        $this->assertDatabaseHas('visit_logs', [
            'doc_number' => $temp->doc_number,
        ]);

        $this->assertDatabaseMissing('visit_logs_temp', [
            'id' => $temp->id,
        ]);
    }

    /**
     * Test Excel and printable document export endpoints
     */
    public function test_excel_export_returns_streamed_response(): void
    {
        $response = $this->get('/export/visit-logs/excel');
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }
}

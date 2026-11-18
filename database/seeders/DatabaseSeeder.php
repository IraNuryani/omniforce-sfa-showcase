<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Complaint;
use App\Models\ComplaintPica;
use App\Models\Customer;
use App\Models\User;
use App\Models\VisitLog;
use App\Models\VisitLogTemp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Operational Areas
        $areas = [
            ['code' => 'JKT-PST', 'name' => 'Jakarta Pusat', 'region' => 'DKI Jakarta'],
            ['code' => 'JKT-SEL', 'name' => 'Jakarta Selatan', 'region' => 'DKI Jakarta'],
            ['code' => 'BDG-KTA', 'name' => 'Bandung Raya', 'region' => 'Jawa Barat'],
            ['code' => 'SBY-TMR', 'name' => 'Surabaya Timur', 'region' => 'Jawa Timur'],
            ['code' => 'MDN-KTA', 'name' => 'Medan Kota', 'region' => 'Sumatera Utara'],
        ];

        $areaModels = [];
        foreach ($areas as $a) {
            $areaModels[] = Area::firstOrCreate(['code' => $a['code']], $a);
        }

        // 2. Seed Users (1 Admin & 3 Field Sales Representatives)
        $admin = User::firstOrCreate(['email' => 'admin@omniforce.io'], [
            'name'        => 'System Administrator',
            'role'        => 'admin',
            'employee_id' => 'EMP-001',
            'password'    => Hash::make('password'),
        ]);

        $reps = [
            ['name' => 'Budi Santoso', 'email' => 'budi@omniforce.io', 'employee_id' => 'SLS-101', 'area_id' => $areaModels[0]->id],
            ['name' => 'Rina Wijaya', 'email' => 'rina@omniforce.io', 'employee_id' => 'SLS-102', 'area_id' => $areaModels[1]->id],
            ['name' => 'Dimas Pratama', 'email' => 'dimas@omniforce.io', 'employee_id' => 'SLS-103', 'area_id' => $areaModels[2]->id],
        ];

        $repModels = [];
        foreach ($reps as $r) {
            $repModels[] = User::firstOrCreate(['email' => $r['email']], array_merge($r, [
                'role'     => 'sales_rep',
                'password' => Hash::make('password'),
            ]));
        }

        // 3. Seed Retail Stores / Customers with real Indonesian coordinates
        $customers = [
            [
                'code' => 'CUST-001', 'name' => 'Toko Sumber Berkat Senen',
                'address' => 'Jl. Kramat Raya No. 45, Senen, Jakarta Pusat',
                'latitude' => -6.1865000, 'longitude' => 106.8450000,
                'area_id' => $areaModels[0]->id, 'store_type' => 'General Trade',
            ],
            [
                'code' => 'CUST-002', 'name' => 'Minimarket Sentosa Cikini',
                'address' => 'Jl. Pegangsaan Timur No. 12, Menteng, Jakarta Pusat',
                'latitude' => -6.1989000, 'longitude' => 106.8427000,
                'area_id' => $areaModels[0]->id, 'store_type' => 'Modern Trade',
            ],
            [
                'code' => 'CUST-003', 'name' => 'Grosir Rezeki Blok M',
                'address' => 'Jl. Melawai Raya No. 8, Kebayoran Baru, Jakarta Selatan',
                'latitude' => -6.2442000, 'longitude' => 106.8015000,
                'area_id' => $areaModels[1]->id, 'store_type' => 'General Trade',
            ],
            [
                'code' => 'CUST-004', 'name' => 'Super Indo Fatmawati',
                'address' => 'Jl. RS Fatmawati No. 22, Cilandak, Jakarta Selatan',
                'latitude' => -6.2921000, 'longitude' => 106.7963000,
                'area_id' => $areaModels[1]->id, 'store_type' => 'Modern Trade',
            ],
            [
                'code' => 'CUST-005', 'name' => 'Toko Laris Manis Dago',
                'address' => 'Jl. Ir. H. Djuanda No. 120, Coblong, Bandung',
                'latitude' => -6.8856000, 'longitude' => 107.6135000,
                'area_id' => $areaModels[2]->id, 'store_type' => 'General Trade',
            ],
        ];

        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = Customer::firstOrCreate(['code' => $c['code']], $c);
        }

        // 4. Seed Historical Completed Visit Logs in Production Table
        $samplePhotos = [
            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
        ];

        if (VisitLog::count() === 0) {
            foreach ($customerModels as $index => $cust) {
                $user = $repModels[$index % count($repModels)];
                $timeIn = now()->subHours(rand(1, 48))->subMinutes(rand(10, 50));
                $duration = rand(15, 45);

                VisitLog::create([
                    'doc_number'         => 'VISIT-HIST-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'user_id'            => $user->id,
                    'customer_id'        => $cust->id,
                    'time_in'            => $timeIn,
                    'time_out'           => (clone $timeIn)->addMinutes($duration),
                    'duration_minutes'   => $duration,
                    'latitude_in'        => $cust->latitude + (rand(-10, 10) * 0.00001),
                    'longitude_in'       => $cust->longitude + (rand(-10, 10) * 0.00001),
                    'selfie_path'        => $samplePhotos[$index % count($samplePhotos)],
                    'notes'              => 'Restocking audit completed, store manager requested marketing flyer.',
                    'is_within_geofence' => ($index !== 3), // Make 1 entry flagged for realistic showcase
                ]);
            }
        }

        // 5. Seed 2 Pending Entries in the Staging Table (VisitLogTemp)
        if (VisitLogTemp::count() === 0) {
            VisitLogTemp::create([
                'doc_number'    => 'VISIT-STAGE-0001',
                'user_id'       => $repModels[0]->id,
                'customer_id'   => $customerModels[0]->id,
                'time_in'       => now()->subMinutes(12),
                'time_out'      => now(),
                'latitude_in'   => $customerModels[0]->latitude,
                'longitude_in'  => $customerModels[0]->longitude,
                'selfie_path'   => $samplePhotos[0],
                'notes'         => 'Ingested from mobile client in low signal area; pending background sync.',
                'sync_attempts' => 0,
            ]);

            VisitLogTemp::create([
                'doc_number'    => 'VISIT-STAGE-0002',
                'user_id'       => $repModels[1]->id,
                'customer_id'   => $customerModels[2]->id,
                'time_in'       => now()->subMinutes(5),
                'time_out'      => now(),
                'latitude_in'   => $customerModels[2]->latitude,
                'longitude_in'  => $customerModels[2]->longitude,
                'selfie_path'   => $samplePhotos[1],
                'notes'         => 'Offline check-in queued.',
                'sync_attempts' => 0,
            ]);
        }

        // 6. Seed Complaints & PICA Records
        if (Complaint::count() === 0) {
            $cmp1 = Complaint::create([
                'ticket_number'     => 'CMP-2024-001',
                'customer_id'       => $customerModels[0]->id,
                'user_id'           => $repModels[0]->id,
                'category'          => 'Shortage Delivery',
                'item_name'         => 'Choco Crunch Bar 45g (Box of 24)',
                'quantity_affected' => 5,
                'status'            => 'OPEN',
                'description'       => 'Delivery invoice stated 20 boxes dispatched, but only 15 boxes received by store receiving clerk.',
            ]);

            ComplaintPica::create([
                'complaint_id'           => $cmp1->id,
                'problem_identification' => 'Discrepancy of 5 boxes noted upon delivery receipt at Toko Sumber Berkat.',
                'root_cause_analysis'    => 'Pallet sorting error at Main Warehouse Loading Bay 2 during dawn dispatch rush. Loose carton staging area was not cross-verified with vehicle trip manifest.',
                'corrective_action'      => 'Dispatched 5 shortage boxes on afternoon express route; customer issued delivery acknowledgment voucher.',
                'preventive_action'      => 'Enforced two-person sign-off on loose carton staging before delivery driver seal application.',
                'pic_name'               => 'Hendra Setiawan (Warehouse Operations)',
                'target_completion_date' => now()->addDays(2)->format('Y-m-d'),
                'status'                 => 'PENDING_AUDIT',
            ]);

            $cmp2 = Complaint::create([
                'ticket_number'     => 'CMP-2024-002',
                'customer_id'       => $customerModels[2]->id,
                'user_id'           => $repModels[1]->id,
                'category'          => 'Product Defect',
                'item_name'         => 'Natural Mineral Water 600ml',
                'quantity_affected' => 2,
                'status'            => 'RESOLVED',
                'description'       => 'Two outer carton cases were compressed and leaking due to improper heavy stacking.',
            ]);

            ComplaintPica::create([
                'complaint_id'           => $cmp2->id,
                'problem_identification' => 'Carton crushing observed on tier 4 of pallet stack in delivery truck.',
                'root_cause_analysis'    => 'Delivery vehicle loader stacked heavier canned goods above beverage cartons contrary to pallet stacking guidelines.',
                'corrective_action'      => 'Damaged goods retrieved and returned for credit note; store re-supplied with intact stock.',
                'preventive_action'      => 'Refreshed training for third-party logistics loading crew on weight distribution limits.',
                'pic_name'               => 'Agus Santoso (QA Logistics Lead)',
                'target_completion_date' => now()->subDay()->format('Y-m-d'),
                'status'                 => 'VERIFIED',
            ]);
        }
    }
}

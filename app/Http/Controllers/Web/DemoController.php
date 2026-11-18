<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\User;
use App\Models\VisitLog;
use App\Models\VisitLogTemp;

class DemoController extends Controller
{
    /**
     * Mobile Phone Simulator view
     */
    public function mobile()
    {
        $salesReps = User::where('role', 'sales_rep')->get();
        $customers = Customer::with('area')->get();
        $stagingCount = VisitLogTemp::count();

        return view('demo.mobile', compact('salesReps', 'customers', 'stagingCount'));
    }

    /**
     * Web Admin Console & Document Center view
     */
    public function admin()
    {
        $totalVisits = VisitLog::count();
        $validVisits = VisitLog::where('is_within_geofence', true)->count();
        $complianceRate = $totalVisits > 0 ? round(($validVisits / $totalVisits) * 100, 1) : 100;
        $stagingCount = VisitLogTemp::count();
        $openComplaints = Complaint::where('status', 'OPEN')->count();

        $visitLogs = VisitLog::with(['user', 'customer.area'])->latest()->limit(15)->get();
        $stagingLogs = VisitLogTemp::with(['user', 'customer'])->latest()->get();
        $complaints = Complaint::with(['customer', 'user', 'pica'])->latest()->get();

        return view('demo.admin', compact(
            'totalVisits',
            'validVisits',
            'complianceRate',
            'stagingCount',
            'openComplaints',
            'visitLogs',
            'stagingLogs',
            'complaints'
        ));
    }

    /**
     * Interactive API Documentation view
     */
    public function docs()
    {
        return view('demo.docs');
    }
}

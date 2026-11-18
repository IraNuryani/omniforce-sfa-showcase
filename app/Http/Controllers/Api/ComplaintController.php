<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintPica;
use App\Traits\DocumentNumber;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    use DocumentNumber;

    public function index()
    {
        $complaints = Complaint::with(['customer', 'user', 'pica'])->latest()->get();
        return self::crudSuccess($complaints, 'Complaints retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'user_id'           => 'required|exists:users,id',
            'category'          => 'required|string',
            'item_name'         => 'required|string',
            'quantity_affected' => 'required|integer|min:1',
            'description'       => 'required|string',
        ]);

        $ticket = self::generateSequenceNumber('CMP');

        $complaint = Complaint::create([
            'ticket_number'     => $ticket,
            'customer_id'       => $validated['customer_id'],
            'user_id'           => $validated['user_id'],
            'category'          => $validated['category'],
            'item_name'         => $validated['item_name'],
            'quantity_affected' => $validated['quantity_affected'],
            'description'       => $validated['description'],
            'status'            => 'OPEN',
        ]);

        // Auto-initialize PICA investigation record
        ComplaintPica::create([
            'complaint_id'           => $complaint->id,
            'problem_identification' => "Reported by field sales: {$validated['item_name']} ({$validated['quantity_affected']} units) issue: {$validated['description']}",
            'root_cause_analysis'    => 'Under investigation by QA and Warehouse Dispatch team.',
            'corrective_action'      => 'Awaiting physical sample verification.',
            'preventive_action'      => 'Standard inbound checklist audit.',
            'pic_name'               => 'QA Lead & Operations',
            'target_completion_date' => now()->addDays(3)->format('Y-m-d'),
            'status'                 => 'PENDING_AUDIT',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Complaint filed and PICA investigation initiated',
            'data'    => $complaint->load('pica'),
        ], 201);
    }
}

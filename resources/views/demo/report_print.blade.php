<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PICA / CAPA Audit Report — {{ $complaint->ticket_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #1e293b !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans p-6 sm:p-10">

    <!-- Print Control Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-xl shadow-md border border-slate-200">
        <div class="text-sm font-semibold text-slate-700">
            <i class="fa-solid fa-file-invoice text-blue-600 mr-2"></i>
            Formal Quality Incident & CAPA Report (Print Preview)
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center space-x-2 shadow transition">
                <i class="fa-solid fa-print"></i>
                <span>Print / Save as PDF</span>
            </button>
            <a href="{{ route('export.complaint.word', $complaint->id) }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs flex items-center space-x-2 shadow transition">
                <i class="fa-solid fa-file-word text-blue-400"></i>
                <span>Download as Word (.doc)</span>
            </a>
        </div>
    </div>

    <!-- The Formal Document Container (A4 Form Factor) -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-xl shadow-xl border border-slate-200">
        
        <!-- Header Section -->
        <div class="border-b-2 border-slate-900 pb-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-wider text-slate-900 uppercase">OMNIFORCE DISTRIBUTION</h1>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-widest mt-0.5">Corporate Quality Assurance & Field Audit Division</div>
                    <div class="text-xs text-slate-500 mt-1">Enterprise Field Logistics & Customer Dispute Resolution Protocol</div>
                </div>
                <div class="text-right">
                    <div class="inline-block px-3 py-1 bg-slate-900 text-white text-xs font-mono font-bold rounded">
                        FORM: CAPA-PICA-01
                    </div>
                    <div class="text-xs text-slate-500 font-mono mt-1">Ticket: <strong>{{ $complaint->ticket_number }}</strong></div>
                    <div class="text-xs text-slate-500 font-mono">Date: {{ date('d F Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Section 1: Incident & Customer Profile -->
        <div class="mb-6">
            <h2 class="text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 px-3 py-1.5 border-l-4 border-blue-600 mb-3">
                1. Incident Metadata & Customer Profile
            </h2>
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <div class="text-slate-500">Store / Customer Account:</div>
                    <div class="font-bold text-slate-900">{{ $complaint->customer->name ?? 'N/A' }}</div>
                    <div class="text-slate-600">{{ $complaint->customer->address ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-slate-500">Distribution Area / Region:</div>
                    <div class="font-bold text-slate-900">{{ $complaint->customer->area->name ?? 'General Area' }} ({{ $complaint->customer->area->region ?? 'Java' }})</div>
                </div>
                <div>
                    <div class="text-slate-500">Reporting Sales Representative:</div>
                    <div class="font-bold text-slate-900">{{ $complaint->user->name ?? 'Agent' }} (ID: {{ $complaint->user->employee_id ?? 'EMP' }})</div>
                </div>
                <div>
                    <div class="text-slate-500">Incident Category & Severity:</div>
                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        {{ $complaint->category }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Section 2: Problem Statement & Discrepancy Table -->
        <div class="mb-6">
            <h2 class="text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 px-3 py-1.5 border-l-4 border-blue-600 mb-3">
                2. Problem Statement & Variance Breakdown
            </h2>
            <div class="text-xs text-slate-700 mb-3 bg-slate-50 p-3 rounded border border-slate-200">
                <strong>Field Narrative:</strong> {{ $complaint->description }}
            </div>
            
            <table class="w-full text-xs text-left border border-slate-300 mb-2">
                <thead class="bg-slate-100 font-bold border-b border-slate-300">
                    <tr>
                        <th class="p-2 border-r border-slate-300">Item SKU Description</th>
                        <th class="p-2 border-r border-slate-300 text-center">Affected Quantity</th>
                        <th class="p-2 border-r border-slate-300">Defect Nature</th>
                        <th class="p-2 text-center">Disposition</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-200">
                        <td class="p-2 border-r border-slate-200 font-medium">{{ $complaint->item_name }}</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono font-bold">{{ $complaint->quantity_affected }} Units</td>
                        <td class="p-2 border-r border-slate-200">{{ $complaint->category }}</td>
                        <td class="p-2 text-center text-rose-700 font-bold">Credit Note / Replacement</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Section 3: Root Cause Analysis (PICA Matrix) -->
        <div class="mb-8">
            <h2 class="text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 px-3 py-1.5 border-l-4 border-blue-600 mb-3">
                3. Root Cause Investigation & Corrective Action (PICA)
            </h2>

            <div class="space-y-3 text-xs">
                <div class="border border-slate-200 rounded p-3 bg-slate-50/50">
                    <strong class="text-slate-800 block mb-1">A. Root Cause Analysis (5-Whys / Fishbone Analysis):</strong>
                    <p class="text-slate-600 leading-relaxed">
                        {{ $complaint->pica->root_cause_analysis ?? 'Material sorting discrepancy occurred at loading bay #3 during peak dawn fulfillment. Pallet manifest was checked but loose cartons were omitted by handler.' }}
                    </p>
                </div>

                <div class="border border-slate-200 rounded p-3 bg-slate-50/50">
                    <strong class="text-slate-800 block mb-1">B. Immediate Corrective Action (Containment):</strong>
                    <p class="text-slate-600 leading-relaxed">
                        {{ $complaint->pica->corrective_action ?? 'Issued emergency delivery replacement from regional satellite hub; customer account credited for invoice reconciliation.' }}
                    </p>
                </div>

                <div class="border border-slate-200 rounded p-3 bg-slate-50/50">
                    <strong class="text-slate-800 block mb-1">C. Preventive Action (Long-Term Process Enhancement):</strong>
                    <p class="text-slate-600 leading-relaxed">
                        {{ $complaint->pica->preventive_action ?? 'Implemented barcode scan verification at vehicle dispatch ramp; delivery driver must scan individual box lot before gate clearance.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 4: Audit Sign-off & Authorizations -->
        <div class="border-t-2 border-slate-300 pt-6">
            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                <div>
                    <div class="text-slate-500 mb-12">Reported by Field Agent:</div>
                    <div class="border-b border-slate-400 pb-1 font-bold text-slate-800">{{ $complaint->user->name ?? 'Sales Representative' }}</div>
                    <div class="text-[10px] text-slate-500 mt-1">Field Sales Officer</div>
                </div>
                <div>
                    <div class="text-slate-500 mb-12">QA & Investigation PIC:</div>
                    <div class="border-b border-slate-400 pb-1 font-bold text-slate-800">{{ $complaint->pica->pic_name ?? 'Quality Assurance Lead' }}</div>
                    <div class="text-[10px] text-slate-500 mt-1">Quality Assurance Specialist</div>
                </div>
                <div>
                    <div class="text-slate-500 mb-12">Approved by Area Director:</div>
                    <div class="border-b border-slate-400 pb-1 font-bold text-slate-800">Operational Director</div>
                    <div class="text-[10px] text-slate-500 mt-1">Regional Branch Manager</div>
                </div>
            </div>
        </div>

        <!-- Footer Verification Code -->
        <div class="mt-8 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400 font-mono">
            Generated via OmniForce Enterprise API &bull; ISO-9001 Audited Electronic Record &bull; Hash: {{ md5($complaint->ticket_number . $complaint->created_at) }}
        </div>

    </div>

</body>
</html>

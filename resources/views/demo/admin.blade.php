@extends('layouts.app')

@section('title', 'Web Admin Console & Document Center — OmniForce SFA')

@section('content')
<div class="space-y-8">
    
    <!-- Top Bar with Header & Quick Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-slate-800/40 p-6 rounded-2xl border border-slate-700/60 backdrop-blur">
        <div>
            <div class="flex items-center space-x-2 text-indigo-400 text-xs font-semibold uppercase tracking-wider">
                <i class="fa-solid fa-server"></i>
                <span>Enterprise Management Console</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">
                Field Operations & Document Center
            </h1>
            <p class="text-slate-400 text-xs sm:text-sm mt-1">
                Real-time route compliance, incident tracking (PICA), and template-driven reporting engine.
            </p>
        </div>

        <!-- Document Export Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('export.visit_logs.excel') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs flex items-center space-x-2 shadow-lg shadow-emerald-600/20 transition">
                <i class="fa-solid fa-file-excel text-sm"></i>
                <span>Export Visit Audit (.xlsx)</span>
            </a>
            @if($complaints->count() > 0)
            <a href="{{ route('export.complaint.print', $complaints->first()->id) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs flex items-center space-x-2 shadow-lg shadow-blue-600/20 transition">
                <i class="fa-solid fa-print text-sm"></i>
                <span>Print CAPA Audit Report</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Executive Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Visits -->
        <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-5 shadow-lg">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Completed Field Visits</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-store"></i>
                </div>
            </div>
            <div class="mt-3 text-2xl font-extrabold text-white">{{ $totalVisits }}</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center space-x-1">
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                <span>Synchronized in Production DB</span>
            </div>
        </div>

        <!-- Metric 2: Compliance Rate -->
        <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-5 shadow-lg">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Geofence Compliance</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>
            <div class="mt-3 text-2xl font-extrabold text-emerald-400">{{ $complianceRate }}%</div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $validVisits }} of {{ $totalVisits }} within 250m radius
            </div>
        </div>

        <!-- Metric 3: Staging Queue -->
        <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-5 shadow-lg">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Staging Ingestion Queue</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
            <div class="mt-3 text-2xl font-extrabold text-amber-300">{{ $stagingCount }}</div>
            <div class="text-[11px] text-slate-400 mt-1">
                Pending atomic sync worker
            </div>
        </div>

        <!-- Metric 4: Open Complaints -->
        <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-5 shadow-lg">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Quality Incidents (PICA)</span>
                <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3 text-2xl font-extrabold text-rose-400">{{ $openComplaints }}</div>
            <div class="text-[11px] text-slate-400 mt-1">
                Shortages & transit defects logged
            </div>
        </div>
    </div>

    <!-- Table 1: Field Visits Audit Trail -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700/60 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-list-check text-blue-400"></i>
                <h2 class="text-base font-bold text-white">Production Visit Audit Logs</h2>
                <span class="text-xs text-slate-400">({{ $visitLogs->count() }} recent entries)</span>
            </div>
            <a href="{{ route('export.visit_logs.excel') }}" class="text-xs text-blue-400 hover:text-blue-300 font-medium">
                <i class="fa-solid fa-download mr-1"></i> Download Spreadsheet
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/60 text-slate-400 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3">Doc #</th>
                        <th class="px-5 py-3">Sales Agent</th>
                        <th class="px-5 py-3">Customer / Store</th>
                        <th class="px-5 py-3">Area</th>
                        <th class="px-5 py-3">Check-In Time</th>
                        <th class="px-5 py-3 text-center">Duration</th>
                        <th class="px-5 py-3 text-center">Proof / Geotag</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($visitLogs as $log)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-5 py-3.5 font-mono text-slate-200 font-semibold">{{ $log->doc_number }}</td>
                        <td class="px-5 py-3.5 font-medium text-white">{{ $log->user->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3.5">
                            <div class="text-slate-200 font-medium">{{ $log->customer->name ?? 'Store' }}</div>
                            <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $log->customer->address ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded bg-slate-700/50 text-[11px] text-slate-300">{{ $log->customer->area->name ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-slate-300">{{ $log->time_in ? $log->time_in->format('Y-m-d H:i') : '-' }}</td>
                        <td class="px-5 py-3.5 text-center font-mono">{{ $log->duration_minutes }} min</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($log->selfie_path)
                            <img src="{{ $log->selfie_path }}" class="w-8 h-8 rounded-full object-cover inline-block ring-2 ring-slate-600">
                            @else
                            <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($log->is_within_geofence)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-check mr-1"></i> Valid GPS
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Outside 250m
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                            No visit records found. Use the Mobile Simulator to check in!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table 2: Field Complaints & PICA Management -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700/60 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-clipboard-check text-amber-400"></i>
                <h2 class="text-base font-bold text-white">Customer Complaints & PICA Resolution Audit</h2>
                <span class="text-xs text-slate-400">({{ $complaints->count() }} tickets)</span>
            </div>
            <span class="text-xs text-slate-400 font-mono">ISO / CAPA Audit Standard</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/60 text-slate-400 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3">Ticket #</th>
                        <th class="px-5 py-3">Store Customer</th>
                        <th class="px-5 py-3">Incident Category</th>
                        <th class="px-5 py-3">Item & Affected Qty</th>
                        <th class="px-5 py-3">PICA Root Cause Summary</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Print / Export</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($complaints as $c)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-5 py-3.5 font-mono text-indigo-300 font-semibold">{{ $c->ticket_number }}</td>
                        <td class="px-5 py-3.5 font-medium text-white">{{ $c->customer->name ?? 'Store' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[11px]">{{ $c->category }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="text-slate-200">{{ $c->item_name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $c->quantity_affected }} units affected</div>
                        </td>
                        <td class="px-5 py-3.5 max-w-xs truncate text-slate-400">
                            {{ $c->pica ? $c->pica->root_cause_analysis : 'Pending Analysis' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $c->status === 'OPEN' ? 'bg-amber-500/20 text-amber-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                {{ $c->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center space-x-1.5 whitespace-nowrap">
                            <a href="{{ route('export.complaint.print', $c->id) }}" target="_blank" class="px-2.5 py-1 rounded bg-slate-700 hover:bg-slate-600 text-slate-200 text-[11px] font-semibold transition" title="Printable HTML / PDF Report">
                                <i class="fa-solid fa-print mr-1"></i> Print
                            </a>
                            <a href="{{ route('export.complaint.word', $c->id) }}" class="px-2.5 py-1 rounded bg-blue-900/40 hover:bg-blue-800/50 text-blue-300 text-[11px] font-semibold transition" title="Download Word Document">
                                <i class="fa-solid fa-file-word mr-1"></i> Word
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                            No complaints recorded.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

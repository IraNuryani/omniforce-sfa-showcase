@extends('layouts.app')

@section('title', 'API Specifications & Live Explorer — OmniForce SFA')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-slate-800/40 p-6 rounded-2xl border border-slate-700/60 backdrop-blur">
        <div class="flex items-center space-x-2 text-emerald-400 text-xs font-semibold uppercase tracking-wider">
            <i class="fa-solid fa-code"></i>
            <span>Developer Reference</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">RESTful API Endpoints Spec</h1>
        <p class="text-slate-400 text-xs sm:text-sm mt-1">
            Standardized envelope format: <code class="text-emerald-400 font-mono">{ status: true, message: string, data: [...], meta: {...} }</code>.
        </p>
    </div>

    <!-- Endpoint 1: POST /api/v1/visit-logs -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-xl overflow-hidden shadow-lg">
        <div class="px-5 py-3.5 bg-slate-900/60 flex items-center justify-between border-b border-slate-700/60">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold font-mono bg-blue-600 text-white">POST</span>
                <span class="text-sm font-mono text-slate-200 font-semibold">/api/v1/visit-logs</span>
            </div>
            <span class="text-xs text-slate-400">Resilient Staging Ingestion</span>
        </div>
        <div class="p-5 text-xs space-y-3">
            <p class="text-slate-300">
                Pushes a field attendance record into the staging queue (<code class="text-blue-300">visit_log_temps</code>) in non-blocking mode. Returns HTTP 201 Created immediately.
            </p>
            <div class="bg-slate-950 p-3 rounded-lg border border-slate-800 font-mono text-slate-300">
                <div class="text-slate-500">// Request Payload Example:</div>
                <pre>{
  "user_id": 1,
  "customer_id": 1,
  "latitude_in": -6.2088000,
  "longitude_in": 106.8456000,
  "notes": "Store visit completed, shelf audit verified.",
  "selfie_image": "https://.../selfie.jpg"
}</pre>
            </div>
        </div>
    </div>

    <!-- Endpoint 2: GET /api/v1/visit-logs/staging-queue -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-xl overflow-hidden shadow-lg">
        <div class="px-5 py-3.5 bg-slate-900/60 flex items-center justify-between border-b border-slate-700/60">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold font-mono bg-emerald-600 text-white">GET</span>
                <span class="text-sm font-mono text-slate-200 font-semibold">/api/v1/visit-logs/staging-queue</span>
            </div>
            <span class="text-xs text-slate-400">Monitor Pending Buffer</span>
        </div>
        <div class="p-5 text-xs space-y-3">
            <p class="text-slate-300">
                Fetches all pending temporary logs awaiting background worker sync. Useful for supervisor monitoring and retry diagnostics.
            </p>
            <div class="flex items-center space-x-3">
                <a href="/api/v1/visit-logs/staging-queue" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs flex items-center space-x-1.5">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Execute in Browser</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Endpoint 3: POST /api/v1/visit-logs/sync -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-xl overflow-hidden shadow-lg">
        <div class="px-5 py-3.5 bg-slate-900/60 flex items-center justify-between border-b border-slate-700/60">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold font-mono bg-amber-600 text-white">POST</span>
                <span class="text-sm font-mono text-slate-200 font-semibold">/api/v1/visit-logs/sync</span>
            </div>
            <span class="text-xs text-slate-400">Trigger Synchronizer</span>
        </div>
        <div class="p-5 text-xs space-y-3">
            <p class="text-slate-300">
                Dispatches the Artisan console command <code class="text-emerald-300">visitlogs:sync</code>. Evaluates Haversine distance, checks idempotency, moves rows atomically, and outputs execution logs.
            </p>
        </div>
    </div>

    <!-- Endpoint 4: GET /api/v1/visit-logs/analytics -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-xl overflow-hidden shadow-lg">
        <div class="px-5 py-3.5 bg-slate-900/60 flex items-center justify-between border-b border-slate-700/60">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold font-mono bg-emerald-600 text-white">GET</span>
                <span class="text-sm font-mono text-slate-200 font-semibold">/api/v1/visit-logs/analytics</span>
            </div>
            <span class="text-xs text-slate-400">Aggregated Metrics</span>
        </div>
        <div class="p-5 text-xs space-y-3">
            <p class="text-slate-300">
                Computes real-time KPIs: total visits, valid visits within 250m geofence, compliance rate percentage, and pending staging buffer count.
            </p>
            <div class="flex items-center space-x-3">
                <a href="/api/v1/visit-logs/analytics" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs flex items-center space-x-1.5">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Execute in Browser</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

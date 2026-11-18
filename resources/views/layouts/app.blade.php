<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OmniForce SFA Showcase') — Enterprise Field Force & Distribution API</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col font-sans antialiased">
    <!-- Top Navigation Header -->
    <header class="bg-slate-800/80 backdrop-blur border-b border-slate-700/60 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-satellite-dish text-white text-lg"></i>
                </div>
                <div>
                    <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">OmniForce SFA</span>
                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 font-mono">LITE SHOWCASE</span>
                </div>
            </div>

            <!-- View Switcher Tabs -->
            <nav class="flex items-center space-x-1 sm:space-x-2 bg-slate-900/60 p-1.5 rounded-xl border border-slate-700/50">
                <a href="{{ route('demo.mobile') }}" class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all {{ request()->routeIs('demo.mobile*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                    <i class="fa-solid fa-mobile-screen mr-1.5"></i> Mobile Simulator
                </a>
                <a href="{{ route('demo.admin') }}" class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all {{ request()->routeIs('demo.admin') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                    <i class="fa-solid fa-gauge-high mr-1.5"></i> Web Admin & Reports
                </a>
                <a href="{{ route('demo.docs') }}" class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all {{ request()->routeIs('demo.docs') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                    <i class="fa-solid fa-code mr-1.5"></i> API Specs
                </a>
            </nav>

            <!-- GitHub Badges -->
            <div class="hidden md:flex items-center space-x-3">
                <span class="text-xs text-slate-400 font-mono">Backend Portfolio Architecture</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950/60 border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        <p>OmniForce SFA Lite — Reference Architecture for Enterprise Sales Force Automation & Resilient Staging Pipelines.</p>
        <p class="mt-1">Demonstrating: Offline-First Ingestion, Haversine Geofencing, Dynamic Staging Queues, and Template-Driven Document Generation.</p>
    </footer>
</body>
</html>

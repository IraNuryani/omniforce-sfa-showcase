@extends('layouts.app')

@section('title', 'Mobile Sales Rep Simulator — OmniForce SFA')

@section('content')
<div x-data="mobileSimulator()" x-init="initData()" class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Left Column: Explanation of the Backend Architecture (Why it matters to recruiters) -->
    <div class="lg:col-span-6 space-y-6">
        <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-xs font-semibold mb-4 border border-blue-500/20">
                <i class="fa-solid fa-bolt"></i>
                <span>Architectural Showcase</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Mobile Field Force Simulator
            </h1>
            <p class="text-slate-300 text-sm mt-2 leading-relaxed">
                Field sales representatives frequently work in rural areas or concrete store basements with spotty cellular reception. 
                Instead of risking network timeouts or lost check-in data, this backend implements a <strong class="text-blue-400">Resilient Staging Queue Pattern</strong>.
            </p>

            <div class="mt-6 space-y-3.5 border-t border-slate-700/60 pt-5 text-xs text-slate-300">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 mt-0.5 font-bold">1</div>
                    <div>
                        <strong class="text-white block font-medium">Non-Blocking Staging Ingestion</strong>
                        The mobile app submits visit data to <code class="text-blue-300">/api/v1/visit-logs</code>, which writes directly into <code class="text-slate-200">visit_log_temps</code> in under 20ms.
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 mt-0.5 font-bold">2</div>
                    <div>
                        <strong class="text-white block font-medium">Haversine Anti-Fraud Geofencing</strong>
                        When syncing, the backend calculates the geodesic distance against the store's verified GPS coordinate (threshold: 250 meters).
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 font-bold">3</div>
                    <div>
                        <strong class="text-white block font-medium">Scheduled Atomic Synchronization</strong>
                        An Artisan command (<code class="text-emerald-300">visitlogs:sync</code>) processes staging rows with transaction rollback and exponential backoff retry.
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Staging Queue Monitor Card -->
        <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </div>
                    <span class="text-sm font-semibold text-white">Live Staging Queue Buffer</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30" x-text="stagingCount + ' Pending in Temp Table'">
                    {{ $stagingCount }} Pending in Temp Table
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-2">
                Clicking the button below manually executes the background worker (<code class="text-slate-300">php artisan visitlogs:sync</code>) to process pending items atomically into production.
            </p>

            <button @click="triggerSync()" :disabled="isSyncing || stagingCount === 0" class="mt-4 w-full py-2.5 px-4 rounded-xl text-xs font-semibold flex items-center justify-center space-x-2 transition-all shadow-lg" :class="stagingCount > 0 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-emerald-500/20 cursor-pointer' : 'bg-slate-700/50 text-slate-500 cursor-not-allowed'">
                <i class="fa-solid fa-arrows-rotate" :class="{'fa-spin': isSyncing}"></i>
                <span x-text="isSyncing ? 'Processing Database Transaction...' : 'Run Artisan Background Sync Now'"></span>
            </button>

            <!-- Sync Log Terminal Window -->
            <div x-show="syncLog" x-cloak class="mt-3 bg-slate-950 p-3 rounded-lg border border-slate-800 text-[11px] font-mono text-emerald-400 max-h-28 overflow-y-auto">
                <div class="text-slate-500 border-b border-slate-800 pb-1 mb-1">// Artisan Command Terminal Output:</div>
                <div x-text="syncLog"></div>
            </div>
        </div>
    </div>

    <!-- Right Column: Interactive Smartphone Mockup -->
    <div class="lg:col-span-6 flex justify-center">
        <div class="w-full max-w-[360px] bg-slate-950 rounded-[44px] p-3.5 shadow-2xl border-4 border-slate-700/80 ring-1 ring-white/10 relative">
            
            <!-- Phone Speaker & Camera Notch -->
            <div class="absolute top-6 left-1/2 -translate-x-1/2 w-28 h-4 bg-slate-900 rounded-full flex items-center justify-center space-x-2 z-20">
                <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                <div class="w-10 h-1.5 rounded-full bg-slate-800"></div>
            </div>

            <!-- Phone Screen Inner Container -->
            <div class="bg-slate-900 rounded-[34px] overflow-hidden border border-slate-800 text-slate-100 flex flex-col min-h-[600px] relative">
                
                <!-- Phone Status Bar -->
                <div class="h-10 bg-slate-900/90 pt-2 px-6 flex items-center justify-between text-[11px] text-slate-400 font-mono select-none">
                    <span x-text="currentTime">09:41</span>
                    <div class="flex items-center space-x-2 text-[10px]">
                        <i class="fa-solid fa-signal"></i>
                        <i class="fa-solid fa-wifi"></i>
                        <i class="fa-solid fa-battery-three-quarters"></i>
                    </div>
                </div>

                <!-- In-App Header -->
                <div class="bg-gradient-to-r from-blue-700 to-indigo-700 px-4 py-3.5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-[10px] text-blue-200 uppercase tracking-wider font-semibold">OmniForce Mobile Agent</div>
                            <div class="text-sm font-bold truncate" x-text="selectedUser ? selectedUser.name : 'Select Agent'"></div>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-4 ring-emerald-400/20"></span>
                    </div>

                    <!-- Navigation Tabs on Mobile -->
                    <div class="flex mt-3 bg-blue-900/40 p-1 rounded-lg text-xs">
                        <button @click="activeTab = 'visit'" :class="activeTab === 'visit' ? 'bg-blue-600 text-white font-semibold' : 'text-blue-200'" class="flex-1 py-1 rounded text-center transition">
                            <i class="fa-solid fa-location-dot mr-1"></i> Check-in
                        </button>
                        <button @click="activeTab = 'complaint'" :class="activeTab === 'complaint' ? 'bg-blue-600 text-white font-semibold' : 'text-blue-200'" class="flex-1 py-1 rounded text-center transition">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Complaint
                        </button>
                    </div>
                </div>

                <!-- Screen Body -->
                <div class="flex-1 p-4 overflow-y-auto space-y-4 text-xs">
                    
                    <!-- TAB 1: VISIT CHECK-IN -->
                    <div x-show="activeTab === 'visit'" class="space-y-3.5">
                        
                        <!-- Step 1: Select Sales Representative -->
                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Sales Representative:</label>
                            <select x-model="form.user_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-blue-500">
                                @foreach($salesReps as $rep)
                                <option value="{{ $rep->id }}" data-name="{{ $rep->name }}">{{ $rep->name }} ({{ $rep->employee_id }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Target Retail Customer -->
                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Target Customer / Store:</label>
                            <select x-model="form.customer_id" @change="onCustomerChange()" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-blue-500">
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" data-lat="{{ $c->latitude }}" data-lng="{{ $c->longitude }}" data-name="{{ $c->name }}">{{ $c->name }} ({{ $c->area->name ?? 'Area' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 3: Mock Geolocation Coordinates -->
                        <div class="bg-slate-800/80 p-3 rounded-xl border border-slate-700/80 space-y-2">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-semibold text-slate-300"><i class="fa-solid fa-location-crosshairs mr-1 text-blue-400"></i> GPS Simulation</span>
                                <span class="text-xs font-mono" :class="isNearGeofence ? 'text-emerald-400' : 'text-rose-400'" x-text="isNearGeofence ? 'Inside Radius (Valid)' : 'Outside Radius (500m)'"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-[10px] text-slate-400 font-mono">
                                <div class="bg-slate-900/60 p-1.5 rounded">Lat: <span class="text-white" x-text="form.latitude_in"></span></div>
                                <div class="bg-slate-900/60 p-1.5 rounded">Lng: <span class="text-white" x-text="form.longitude_in"></span></div>
                            </div>
                            <div class="flex space-x-2 pt-1">
                                <button type="button" @click="setGeofenceMode(true)" class="flex-1 py-1 rounded text-[10px] font-semibold border" :class="isNearGeofence ? 'bg-emerald-600/30 text-emerald-300 border-emerald-500/50' : 'bg-slate-700/40 text-slate-400 border-transparent'">
                                    Exact Location
                                </button>
                                <button type="button" @click="setGeofenceMode(false)" class="flex-1 py-1 rounded text-[10px] font-semibold border" :class="!isNearGeofence ? 'bg-rose-600/30 text-rose-300 border-rose-500/50' : 'bg-slate-700/40 text-slate-400 border-transparent'">
                                    Fake / 500m Away
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Selfie Photo Proof Mockup -->
                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Store Selfie & Attendance Proof:</label>
                            <div class="flex items-center space-x-3 bg-slate-800/80 p-2.5 rounded-xl border border-slate-700">
                                <img :src="selfiePreview" class="w-12 h-12 rounded-lg object-cover ring-2 ring-blue-500/30">
                                <div class="flex-1 text-[11px]">
                                    <div class="text-white font-medium">Selfie_Verified_GPS.jpg</div>
                                    <div class="text-slate-400 text-[10px] flex items-center space-x-1">
                                        <i class="fa-solid fa-lock text-emerald-400"></i>
                                        <span>Timestamp & Geotag Embedded</span>
                                    </div>
                                </div>
                                <button type="button" @click="toggleSelfieImage()" class="p-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200">
                                    <i class="fa-solid fa-camera"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 5: Notes -->
                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Visit Interaction Notes:</label>
                            <input type="text" x-model="form.notes" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-blue-500" placeholder="Product audit completed, order placed...">
                        </div>

                        <!-- Submit Button -->
                        <button type="button" @click="submitVisit()" :disabled="isSubmitting" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition">
                            <i class="fa-solid fa-cloud-arrow-up" x-show="!isSubmitting"></i>
                            <i class="fa-solid fa-spinner fa-spin" x-show="isSubmitting" x-cloak></i>
                            <span x-text="isSubmitting ? 'Pushing to Staging Queue...' : 'Check-In & Save to Staging Table'"></span>
                        </button>

                    </div>

                    <!-- TAB 2: COMPLAINT SUBMISSION -->
                    <div x-show="activeTab === 'complaint'" x-cloak class="space-y-3.5">
                        <div class="bg-amber-500/10 border border-amber-500/20 p-2.5 rounded-xl text-amber-300 text-[11px]">
                            <i class="fa-solid fa-info-circle mr-1"></i> Log store shortages, transit damages, or returns.
                        </div>

                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Issue Category:</label>
                            <select x-model="complaintForm.category" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-blue-500">
                                <option value="Shortage Delivery">Shortage Delivery (Missing Qty)</option>
                                <option value="Product Defect">Product Defect / Quality Damage</option>
                                <option value="Packaging Broken">Packaging Seal Broken in Transit</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Affected Item SKU:</label>
                            <input type="text" x-model="complaintForm.item_name" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-blue-500" placeholder="e.g. Milk UHT 1000ml (Carton)">
                        </div>

                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Affected Quantity (Pcs/Box):</label>
                            <input type="number" x-model="complaintForm.quantity_affected" min="1" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Problem Description:</label>
                            <textarea x-model="complaintForm.description" rows="2" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-blue-500" placeholder="Describe the physical condition..."></textarea>
                        </div>

                        <button type="button" @click="submitComplaint()" :disabled="isSubmittingComplaint" class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-bold text-xs shadow-lg shadow-amber-500/25 flex items-center justify-center space-x-2 transition">
                            <i class="fa-solid fa-paper-plane" x-show="!isSubmittingComplaint"></i>
                            <i class="fa-solid fa-spinner fa-spin" x-show="isSubmittingComplaint" x-cloak></i>
                            <span x-text="isSubmittingComplaint ? 'Registering Ticket...' : 'File Ticket & Trigger PICA Protocol'"></span>
                        </button>
                    </div>

                    <!-- Submission Success Toast Alert -->
                    <div x-show="successMessage" x-cloak class="p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-[11px] flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                        <span x-text="successMessage"></span>
                    </div>

                </div>

                <!-- Bottom Navigation Bar Indicator -->
                <div class="h-6 bg-slate-900 flex items-center justify-center">
                    <div class="w-32 h-1 bg-slate-700 rounded-full"></div>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
function mobileSimulator() {
    return {
        activeTab: 'visit',
        currentTime: '09:41',
        isSyncing: false,
        isSubmitting: false,
        isSubmittingComplaint: false,
        stagingCount: {{ $stagingCount }},
        syncLog: '',
        successMessage: '',
        isNearGeofence: true,
        selfiePreview: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
        selectedUser: null,
        form: {
            user_id: '{{ $salesReps->first()->id ?? 1 }}',
            customer_id: '{{ $customers->first()->id ?? 1 }}',
            latitude_in: '{{ $customers->first()->latitude ?? -6.2088 }}',
            longitude_in: '{{ $customers->first()->longitude ?? 106.8456 }}',
            notes: 'Product shelf audit verified, restocking arranged.',
            selfie_image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80'
        },
        complaintForm: {
            customer_id: '{{ $customers->first()->id ?? 1 }}',
            user_id: '{{ $salesReps->first()->id ?? 1 }}',
            category: 'Shortage Delivery',
            item_name: 'Premium Chocolate Wafer 250g',
            quantity_affected: 3,
            description: 'Customer ordered 10 boxes, but only received 7 boxes during morning dispatch delivery.'
        },

        initData() {
            this.updateClock();
            setInterval(() => this.updateClock(), 30000);
            this.selectedUser = { name: '{{ $salesReps->first()->name ?? "Sales Rep" }}' };
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        },

        onCustomerChange() {
            const select = document.querySelector('select[x-model="form.customer_id"]');
            const selectedOpt = select.options[select.selectedIndex];
            const baseLat = parseFloat(selectedOpt.getAttribute('data-lat')) || -6.2088;
            const baseLng = parseFloat(selectedOpt.getAttribute('data-lng')) || 106.8456;

            if (this.isNearGeofence) {
                // Add slight jitter (< 50 meters)
                this.form.latitude_in = (baseLat + (Math.random() - 0.5) * 0.0003).toFixed(7);
                this.form.longitude_in = (baseLng + (Math.random() - 0.5) * 0.0003).toFixed(7);
            } else {
                // Far off (> 500 meters)
                this.form.latitude_in = (baseLat + 0.008).toFixed(7);
                this.form.longitude_in = (baseLng + 0.008).toFixed(7);
            }
            this.complaintForm.customer_id = this.form.customer_id;
        },

        setGeofenceMode(isNear) {
            this.isNearGeofence = isNear;
            this.onCustomerChange();
        },

        toggleSelfieImage() {
            const photos = [
                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
                'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80'
            ];
            const currIdx = photos.indexOf(this.selfiePreview);
            this.selfiePreview = photos[(currIdx + 1) % photos.length];
            this.form.selfie_image = this.selfiePreview;
        },

        async submitVisit() {
            this.isSubmitting = true;
            this.successMessage = '';
            try {
                const response = await fetch('/api/v1/visit-logs', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const res = await response.json();
                if (res.status) {
                    this.stagingCount++;
                    this.successMessage = 'Saved into Staging Table (Pending Sync)';
                    setTimeout(() => this.successMessage = '', 4000);
                }
            } catch (e) {
                alert('API Request Failed: ' + e);
            } finally {
                this.isSubmitting = false;
            }
        },

        async submitComplaint() {
            this.isSubmittingComplaint = true;
            this.successMessage = '';
            try {
                this.complaintForm.user_id = this.form.user_id;
                const response = await fetch('/api/v1/complaints', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(this.complaintForm)
                });
                const res = await response.json();
                if (res.status) {
                    this.successMessage = 'Ticket #' + res.data.ticket_number + ' Filed & PICA Generated!';
                    setTimeout(() => {
                        this.successMessage = '';
                        this.activeTab = 'visit';
                    }, 3500);
                }
            } catch (e) {
                alert('Complaint submission failed: ' + e);
            } finally {
                this.isSubmittingComplaint = false;
            }
        },

        async triggerSync() {
            this.isSyncing = true;
            try {
                const response = await fetch('/api/v1/visit-logs/sync', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' }
                });
                const res = await response.json();
                this.syncLog = res.output;
                this.stagingCount = 0;
            } catch (e) {
                alert('Sync failed: ' + e);
            } finally {
                this.isSyncing = false;
            }
        }
    }
}
</script>
@endsection

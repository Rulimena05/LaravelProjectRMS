<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div id="agent-header-status" class="text-sm text-right">
                <p class="font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-600">Durasi Online: <span id="online-duration" class="font-mono">00:00:00</span></p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-gray-500">Total Panggilan (Hari Ini)</h2>
                        <p class="text-4xl font-semibold">{{ $totalCallsToday }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-gray-500">Kontak Dipanggil (Hari Ini)</h2>
                        <p class="text-4xl font-semibold">{{ $totalUniqueContactsCalledToday }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        @if(auth()->user()->isAdmin())
                            <h2 class="card-title text-gray-500">Panggilan per Agen (Hari Ini)</h2>
                            <div class="mt-2 space-y-1 text-sm">
                                @forelse($callsPerAgentToday as $stat)
                                    <div class="flex justify-between"><span>{{ $stat->user->name ?? 'N/A' }}</span> <span class="font-bold">{{ $stat->total }}</span></div>
                                @empty
                                    <p>Belum ada panggilan.</p>
                                @endforelse
                            </div>
                        @else
                            <h2 class="card-title text-gray-500">Total Kontak Anda</h2>
                            <p class="text-4xl font-semibold">{{ $totalContacts }}</p> 
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                @if(auth()->user()->isAdmin())
                <div class="card bg-base-100 shadow-xl p-6">
                    <h3 class="font-semibold mb-4 text-center">Total PTP per Agen</h3>
                    <canvas id="ptpChart"></canvas>
                </div>
                @endif
                <div class="card bg-base-100 shadow-xl p-6 {{ auth()->user()->isAdmin() ? '' : 'lg:col-span-2' }}">
                    <h3 class="font-semibold mb-4 text-center">Tren Panggilan 30 Hari Terakhir</h3>
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">KPI Agen Real-Time</h2>
                    <div id="kpi-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="text-center p-4">Memuat data agen...</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const loginTime = new Date("{{ auth()->user()->status_updated_at }}");
        const durationSpan = document.getElementById('online-duration');

        function formatDuration(totalSeconds) {
            if (isNaN(totalSeconds) || totalSeconds < 0) totalSeconds = 0;
            const hours = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
            const minutes = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
            const seconds = Math.floor(totalSeconds % 60).toString().padStart(2, '0');
            return `${hours}:${minutes}:${seconds}`;
        }
        
        setInterval(() => {
            const status = "{{ auth()->user()->status }}";
            // Timer hanya berjalan jika statusnya bukan offline atau istirahat
            if (status === "online" || status === "on_a_call") {
                const now = new Date();
                // Hitung durasi sejak waktu status terakhir diupdate (saat login)
                const durationInSeconds = (now - loginTime) / 1000;
                durationSpan.innerText = formatDuration(durationInSeconds);
            } else {
                 durationSpan.innerText = "N/A";
            }
        }, 1000);

        @if(auth()->user()->isAdmin())
            const ptpLabels = @json($ptpLabels);
            const ptpData = @json($ptpData);
            new Chart(document.getElementById('ptpChart'), { type: 'bar', data: { labels: ptpLabels, datasets: [{ label: 'Total Nominal PTP', data: ptpData, backgroundColor: '#22c55e' }] }, options: { indexAxis: 'y' } });
        @endif

        const trendLabels = @json($trendLabels);
        const trendData = @json($trendData);
        new Chart(document.getElementById('trendChart'), { type: 'line', data: { labels: trendLabels, datasets: [{ label: 'Total Panggilan per Hari', data: trendData, borderColor: '#ef4444', tension: 0.1 }] } });
        
        @if(auth()->user()->isAdmin())
            const kpiContainer = document.getElementById('kpi-container');

            async function fetchAgentStatuses() {
                try {
                    const response = await fetch("{{ route('api.kpi.agent_statuses') }}?_=" + new Date().getTime());
                    const agents = await response.json();
                    kpiContainer.innerHTML = '';
                    if (agents.length === 0) {
                        kpiContainer.innerHTML = '<p class="text-center col-span-full">Tidak ada agen yang terdaftar.</p>';
                        return;
                    }
                    agents.forEach(agent => {
                        let durationInSeconds = 0;
                        if (agent.status_updated_at) {
                            const now = new Date();
                            const statusUpdatedAt = new Date(agent.status_updated_at);
                            durationInSeconds = Math.floor((now - statusUpdatedAt) / 1000);
                        }
                        let statusClass = 'badge-ghost';
                        if (agent.status === 'online') statusClass = 'badge-success';
                        if (agent.status === 'offline') statusClass = 'badge-error';
                        if (agent.status === 'on_a_call') statusClass = 'badge-info';
                        if (agent.status === 'on_break') statusClass = 'badge-warning';
                        const agentCard = `<div class="card bg-base-200 shadow"><div class="card-body p-4"><h3 class="font-bold text-lg">${agent.name}</h3><div class="flex justify-between items-center mt-2"><span class="badge ${statusClass} capitalize">${agent.status.replace('_', ' ')}</span><span class="font-mono text-lg">${formatDuration(durationInSeconds)}</span></div></div></div>`;
                        kpiContainer.innerHTML += agentCard;
                    });
                } catch (error) {
                    console.error('Gagal mengambil status agen:', error);
                    kpiContainer.innerHTML = '<p class="text-center text-error col-span-full">Gagal memuat data.</p>';
                }
            }
            fetchAgentStatuses();
            setInterval(fetchAgentStatuses, 1000); // Update setiap 1 detik
        @endif
    </script>
</x-app-layout>
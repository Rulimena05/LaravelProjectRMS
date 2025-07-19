<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Panggilan') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}" class="mb-6 p-4 border rounded-lg bg-base-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Agen</span></label>
                                <select name="agent_id" class="select select-bordered">
                                    <option value="">Semua Agen</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" @selected(request('agent_id') == $agent->id)>{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Dari Tanggal</span></label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Sampai Tanggal</span></label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="input input-bordered">
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" class="btn btn-primary w-1/2">Filter</button>
                                <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success w-1/2">Export</a>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Agen</th>
                                    <th>Kontak</th>
                                    <th>Task ID</th>
                                    <th>Case ID</th>
                                    <th>Hasil</th>
                                    <th>Durasi (hh:mm:ss)</th>
                                    <th>Tgl PTP</th>
                                    <th>Nominal PTP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($callLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                                    <td>{{ $log->user->name ?? 'N/A' }}</td>
                                    <td>{{ $log->contact->name ?? 'N/A' }}</td>
                                    <td>{{ $log->contact->task_id ?? 'N/A' }}</td>
                                    <td>{{ $log->contact->additional_data['case_id'] ?? 'N/A' }}</td>
                                    <td><span class="badge {{ $log->outcome === 'PTP' ? 'badge-accent' : '' }}">{{ $log->outcome }}</span></td>
                                    <td>{{ gmdate("H:i:s", $log->duration_seconds) }}</td>
                                    <td>{{ $log->promiseToPay ? \Carbon\Carbon::parse($log->promiseToPay->ptp_date)->format('d M Y') : '-' }}</td>
                                    <td>{{ $log->promiseToPay ? 'Rp ' . number_format($log->promiseToPay->ptp_amount, 0, ',', '.') : '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="text-center py-4">Tidak ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4"><div class="join">{{ $callLogs->withQueryString()->links() }}</div></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
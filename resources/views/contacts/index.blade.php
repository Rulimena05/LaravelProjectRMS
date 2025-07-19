<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Kontak') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('contacts.create') }}" class="btn btn-neutral btn-sm">
                            Tambah Kontak Baru
                        </a>
                        
                        <form method="GET" action="{{ route('dialer.start') }}">
                            <div class="flex items-center space-x-2">
                                <select name="campaign_id" class="select select-bordered select-sm">
                                    <option value="">Dial Semua Kontak Baru</option>
                                    @foreach($campaigns->where('status', 'active') as $campaign)
                                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Mulai Sesi
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <form method="GET" action="{{ route('contacts.index') }}" class="mb-4 p-4 border rounded-lg bg-base-200">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            <input type="text" name="search" placeholder="Cari nama, no telp, task/case id..." value="{{ request('search') }}" class="input input-bordered w-full md:col-span-2">
                            
                            <select name="campaign_id" class="select select-bordered">
                                <option value="">Semua Campaign</option>
                                @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" @selected(request('campaign_id') == $campaign->id)>{{ $campaign->name }}</option>
                                @endforeach
                            </select>

                            {{-- FILTER STATUS YANG DITAMBAHKAN KEMBALI --}}
                            <select name="status" class="select select-bordered">
                                <option value="">Semua Status</option>
                                <option value="new" @selected(request('status') == 'new')>Baru</option>
                                <option value="dihubungi" @selected(request('status') == 'dihubungi')>Dihubungi</option>
                                <option value="callback" @selected(request('status') == 'callback')>Callback</option>
                                <option value="PTP" @selected(request('status') == 'PTP')>PTP</option>
                            </select>

                            <button type="submit" class="btn btn-outline">Filter</button>
                        </div>
                    </form>

                    @if (session('success'))
                        <div role="alert" class="alert alert-success mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Nomor Telepon</th>
                                    <th>Campaign</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contacts as $contact)
                                    <tr>
                                        <td>
                                            <a href="{{ route('contacts.show', $contact->id) }}" class="link link-hover link-primary font-semibold">
                                                {{ $contact->name }}
                                            </a>
                                        </td>
                                        <td>{{ $contact->phone_number }}</td>
                                        <td>{{ $contact->campaign->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($contact->status == 'new')
                                                <span class="badge badge-info">{{ $contact->status }}</span>
                                            @elseif($contact->status == 'dihubungi')
                                                 <span class="badge badge-success">{{ $contact->status }}</span>
                                            @elseif($contact->status == 'callback' || $contact->status == 'PTP')
                                                 <span class="badge badge-warning">{{ $contact->status }}</span>
                                            @else
                                                <span class="badge badge-ghost">{{ $contact->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data kontak yang ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <div class="join">
                            {{ $contacts->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
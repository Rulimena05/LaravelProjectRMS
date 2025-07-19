<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manajemen Campaign') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="card-title">Daftar Campaign</h2>
                        <a href="{{ route('campaigns.create') }}" class="btn btn-neutral btn-sm">Tambah Campaign Baru</a>
                    </div>

                    @if(session('success')) <div role="alert" class="alert alert-success mb-4"><span>{{ session('success') }}</span></div> @endif

                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                <tr>
                                    <td class="font-semibold">{{ $campaign->name }}</td>
                                    <td>{{ Str::limit($campaign->description, 50) }}</td>
                                    <td><span class="badge {{ $campaign->status == 'active' ? 'badge-success' : 'badge-ghost' }}">{{ $campaign->status }}</span></td>
                                    <td class="text-right">
                                        <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-ghost btn-xs">Edit</a>

                                        {{-- TOMBOL HAPUS BARU --}}
                                        <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus campaign ini? Semua kontak di dalamnya akan menjadi tidak tercategory.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs text-red-500">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Belum ada campaign.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
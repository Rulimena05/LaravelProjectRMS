<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="card-title">Daftar User</h2>
                        <a href="{{ route('users.create') }}" class="btn btn-neutral btn-sm">Tambah Pengguna Baru</a>
                    </div>

                    @if(session('success')) <div role="alert" class="alert alert-success mb-4"><span>{{ session('success') }}</span></div> @endif
                    @if(session('error')) <div role="alert" class="alert alert-error mb-4"><span>{{ session('error') }}</span></div> @endif

                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge {{ $user->role == 'admin' ? 'badge-primary' : 'badge-ghost' }}">{{ $user->role }}</span></td>
                                        <td class="text-right">
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-ghost btn-xs">Edit</a>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-ghost btn-xs text-red-500">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4"><div class="join">{{ $users->links() }}</div></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
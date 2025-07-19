<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Pengguna: ') }}{{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control"><label class="label"><span class="label-text">Nama</span></label><input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input input-bordered w-full"></div>
                            <div class="form-control"><label class="label"><span class="label-text">Email</span></label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input input-bordered w-full"></div>
                            <div class="form-control"><label class="label"><span class="label-text">Password Baru</span></label><input type="password" name="password" class="input input-bordered w-full" placeholder="Kosongkan jika tidak diubah"></div>
                            <div class="form-control"><label class="label"><span class="label-text">Konfirmasi Password Baru</span></label><input type="password" name="password_confirmation" class="input input-bordered w-full"></div>
                            <div class="form-control md:col-span-2"><label class="label"><span class="label-text">Role</span></label><select name="role" class="select select-bordered"><option value="agent" @selected(old('role', $user->role) == 'agent')>Agent</option><option value="admin" @selected(old('role', $user->role) == 'admin')>Admin</option></select></div>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-ghost">Batal</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Pengguna Baru') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control"><label class="label"><span class="label-text">Nama</span></label><input type="text" name="name" required class="input input-bordered w-full"></div>
                            <div class="form-control"><label class="label"><span class="label-text">Email</span></label><input type="email" name="email" required class="input input-bordered w-full"></div>
                            <div class="form-control"><label class="label"><span class="label-text">Password</span></label><input type="password" name="password" required class="input input-bordered w-full"></div>
                            <div class="form-control"><label class="label"><span class="label-text">Konfirmasi Password</span></label><input type="password" name="password_confirmation" required class="input input-bordered w-full"></div>
                            <div class="form-control md:col-span-2"><label class="label"><span class="label-text">Role</span></label><select name="role" class="select select-bordered"><option value="agent">Agent</option><option value="admin">Admin</option></select></div>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-ghost">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
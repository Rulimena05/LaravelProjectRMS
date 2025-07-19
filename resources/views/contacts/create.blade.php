<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Kontak Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <form action="{{ route('contacts.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Nama</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="input input-bordered w-full" />
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Nomor Telepon</span>
                                </label>
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required class="input input-bordered w-full" />
                            </div>

                            <div class="form-control w-full md:col-span-2">
                                <label class="label">
                                    <span class="label-text">Email</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" />
                            </div>

                            <div class="form-control w-full md:col-span-2">
                                <label class="label">
                                    <span class="label-text">Catatan</span>
                                </label>
                                <textarea name="notes" rows="4" class="textarea textarea-bordered">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-6">
                            <a href="{{ route('contacts.index') }}" class="btn btn-ghost">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
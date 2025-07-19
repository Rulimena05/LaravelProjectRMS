<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Campaign Baru</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <form action="{{ route('campaigns.store') }}" method="POST">
                        @csrf
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Nama Campaign</span></label>
                            <input type="text" name="name" required class="input input-bordered w-full" />
                        </div>
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Deskripsi</span></label>
                            <textarea name="description" rows="3" class="textarea textarea-bordered"></textarea>
                        </div>
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Status</span></label>
                            <select name="status" class="select select-bordered">
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="card-actions justify-end mt-4">
                             <a href="{{ route('campaigns.index') }}" class="btn btn-ghost">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
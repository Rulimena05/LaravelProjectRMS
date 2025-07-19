<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Data Kontak') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div role="alert" class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h3 class="font-bold">Instruksi</h3>
                            <div class="text-xs">Pastikan file Excel Anda memiliki header: <strong>name, phone_number, email, notes, agent,</strong> dll.</div>
                            <div>Tidak punya template? <a href="{{ route('data.download_template') }}" class="link link-primary">Unduh di sini</a>.</div>
                        </div>
                    </div>

                    @if (session('error')) <div role="alert" class="alert alert-error mb-4"><span>{{ session('error') }}</span></div>@endif
                    @if ($errors->any()) <div role="alert" class="alert alert-warning mb-4"><span>File tidak valid. Pastikan format .xlsx atau .xls dan semua kolom wajib diisi.</span></div> @endif

                    <form action="{{ route('data.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Pilih Campaign (Wajib)</span></label>
                            <select name="campaign_id" required class="select select-bordered">
                                <option disabled selected>-- Pilih Campaign --</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Pilih File Excel</span></label>
                            <input type="file" name="file" required class="file-input file-input-bordered w-full" />
                        </div>
                        
                        <div class="card-actions justify-end mt-6">
                            <button type="submit" class="btn btn-primary">
                                Upload & Impor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
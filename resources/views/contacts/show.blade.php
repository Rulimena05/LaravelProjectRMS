<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2 sm:mb-0">
                Detail Kontak: {{ $contact->name }}
            </h2>

            <div class="flex items-center space-x-2">
                {{-- TOMBOL BARU --}}
                <a href="{{ route('contacts.next', $contact) }}" class="btn btn-primary btn-sm">Kontak Selanjutnya &rarr;</a>

                <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-error btn-sm">Hapus</button>
                </form>
                <a href="{{ route('contacts.index') }}" class="btn btn-ghost btn-sm">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
            <div role="alert" class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
            @endif

            <div class="flex flex-col md:flex-row md:space-x-6">
                <div class="md:w-1/2 flex flex-col gap-6 mb-6 md:mb-0">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title border-b pb-2">Informasi Kontak</h2>
                            <div class="space-y-3 mt-4">
                                <p><strong>Nama:</strong> {{ $contact->name }}</p>
                                <p><strong>Email:</strong> {{ $contact->email ?? '-' }}</p>
                                <p><strong>Status:</strong>
                                    @if($contact->status == 'new')<span class="badge badge-info">{{ $contact->status }}</span>
                                    @elseif($contact->status == 'dihubungi')<span class="badge badge-success">{{ $contact->status }}</span>
                                    @elseif($contact->status == 'callback')<span class="badge badge-warning">{{ $contact->status }}</span>
                                    @else<span class="badge badge-ghost">{{ $contact->status }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title">Nomor Telepon</h2>
                            <div class="flex items-center mt-2">
                                <span class="w-40">Utama: {{ $contact->phone_number }}</span>
                                <a href="tel:{{ $contact->phone_number }}" class="call-btn btn btn-success btn-sm ml-4">CALL</a>
                            </div>
                            @foreach($contact->phoneNumbers as $phone)
                            <div class="flex items-center mt-2">
                                <span class="w-40 capitalize">{{ $phone->label }}:</span>
                                <span>{{ $phone->number }}</span>
                                <a href="tel:{{ $phone->number }}" class="call-btn btn btn-success btn-sm ml-4">CALL</a>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title">Data Tambahan</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm mt-4">
                                @forelse($contact->additional_data ?? [] as $key => $value)
                                <div>
                                    <strong class="capitalize">{{ str_replace('_', ' ', $key) }}:</strong>
                                    <span>{{ $value }}</span>
                                </div>
                                @empty
                                <p class="col-span-2 text-gray-500">Tidak ada data tambahan.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div id="call-timer-display" class="stats bg-neutral text-neutral-content hidden">
                        <div class="stat">
                            <div class="stat-title text-neutral-content">Waktu Panggilan</div>
                            <div id="timer" class="stat-value">00:00</div>
                        </div>
                    </div>
                </div>

                <div id="report-form-container" class="md:w-1/2">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <div id="call-timer-display" class="stats bg-neutral text-neutral-content hidden mb-4">
                                <div class="stat">
                                    <div class="stat-title text-neutral-content">Waktu Panggilan</div>
                                    <div id="timer" class="stat-value">00:00</div>
                                </div>
                            </div>

                            <form action="{{ route('contacts.log_call', $contact) }}" method="POST">
                                @csrf
                                <input type="hidden" name="duration_seconds" id="duration_seconds" value="0">

                                <h2 class="card-title border-b pb-2 mb-4">Update Laporan Panggilan</h2>

                                <div class="text-xs space-y-1 bg-base-200 p-3 rounded-lg mb-4">
                                    <p><strong>Case ID:</strong> {{ $contact->additional_data['case_id'] ?? 'N/A' }}</p>
                                    <p><strong>Task ID:</strong> {{ $contact->task_id ?? 'N/A' }}</p>
                                    <p><strong>Customer Name:</strong> {{ $contact->name }}</p>
                                    <p><strong>Agent:</strong> {{ auth()->user()->name }}</p>
                                    <p><strong>Tanggal Panggilan:</strong> {{ now()->format('d-m-Y') }}</p>
                                    <p><strong>Waktu Panggilan:</strong> <span id="current-time">{{ now()->format('H:i:s') }}</span></p>
                                </div>

                                <div class="form-control w-full mb-4">
                                    <label class="label"><span class="label-text">Hasil Panggilan</span></label>
                                    <select name="outcome" id="outcome-select" class="select select-bordered" required>
                                        <option value="">-- Pilih Hasil --</option>
                                        <option value="PTP">PTP (Promise To Pay)</option>
                                        <option value="NPT">NPT (Not Promise To Pay)</option>
                                        <option value="PAID LUNAS">PAID LUNAS</option>
                                        <option value="NOANS">NOANS (No Answer)</option>
                                        <option value="NCT">NCT (No Contact)</option>
                                        <option value="BP">BP (Broken Promise)</option>
                                        <option value="SKIP">SKIP</option>
                                        <option value="LMSG">LMSG (Leave Message)</option>
                                    </select>
                                </div>

                                <div id="ptp-section" class="grid grid-cols-2 gap-4 border p-3 rounded-lg mb-4 hidden">
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">Tanggal PTP</span></label>
                                        <input type="date" id="ptp_date" name="ptp_date" class="input input-bordered">
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">Nominal PTP</span></label>
                                        <input type="number" id="ptp_amount" name="ptp_amount" placeholder="Contoh: 100000" class="input input-bordered">
                                    </div>
                                </div>

                                <div class="form-control w-full mb-4">
                                    <label class="label"><span class="label-text">Catatan Tambahan</span></label>
                                    <textarea name="notes" id="notes" rows="3" class="textarea textarea-bordered" required></textarea>
                                </div>
                                <div class="form-control w-full mb-4">
                                    <label class="label"><span class="label-text">Update Phone (Opsional)</span></label>
                                    <input type="text" name="new_phone_number" placeholder="Masukkan nomor telepon baru jika ada" class="input input-bordered">
                                </div>

                                <div class="card-actions justify-end">
                                    <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timerInterval;
        let seconds = 0;
        let timeInterval;

        function updateTimerDisplay() {
            const minutes = Math.floor(seconds / 60).toString().padStart(2, '0');
            const remainingSeconds = (seconds % 60).toString().padStart(2, '0');
            document.getElementById('timer').innerText = `${minutes}:${remainingSeconds}`;
            document.getElementById('duration_seconds').value = seconds;
        }

        document.querySelectorAll('.call-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('report-form-container').classList.remove('hidden');
                document.getElementById('call-timer-display').classList.remove('hidden');
                clearInterval(timerInterval);
                clearInterval(timeInterval);

                seconds = 0;
                updateTimerDisplay();
                timerInterval = setInterval(() => {
                    seconds++;
                    updateTimerDisplay();
                }, 1000);

                // Update Waktu Panggilan real-time
                timeInterval = setInterval(() => {
                    const now = new Date();
                    document.getElementById('current-time').innerText = now.toTimeString().split(' ')[0];
                }, 1000);
            });
        });

        document.querySelectorAll('.call-btn').forEach(button => {
            button.addEventListener('click', function() {
                // KIRIM STATUS BARU KE SERVER
                fetch("{{ route('status.update') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'on_a_call'
                    })
                });
            });
        });

        document.getElementById('outcome-select').addEventListener('change', function() {
            const ptpSection = document.getElementById('ptp-section');
            const ptpDate = document.getElementById('ptp_date');
            const ptpAmount = document.getElementById('ptp_amount');
            if (this.value === 'PTP') {
                ptpSection.classList.remove('hidden');
                ptpDate.required = true;
                ptpAmount.required = true;
            } else {
                ptpSection.classList.add('hidden');
                ptpDate.required = false;
                ptpAmount.required = false;
            }
        });
    </script>
</x-app-layout>
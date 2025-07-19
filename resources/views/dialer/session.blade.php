<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sesi Dialing
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div id="dialer-card" data-contact-id="{{ $contact->id }}" class="p-8 text-center">
                    <h2 id="contact-name" class="text-3xl font-bold">{{ $contact->name }}</h2>
                    <h3 id="contact-phone" class="text-2xl text-gray-600 mt-2">{{ $contact->phone_number }}</h3>
                    <p class="mt-1 text-gray-500">Status: <span id="contact-status">{{ $contact->status }}</span></p>

                    {{-- Tampilan Timer Panggilan BARU --}}
                    <div id="call-timer-display" class="mt-4 p-3 bg-gray-800 text-white rounded-lg text-2xl font-mono">
                        Waktu Panggilan: <span id="timer">00:00</span>
                    </div>

                    <a id="call-link" href="tel:{{ $contact->phone_number }}" style="display:none;">Call</a>

                    <div class="mt-8 border-t pt-6">
                        <p class="text-sm font-semibold text-gray-700 mb-4">PILIH HASIL PANGGILAN:</p>
                        <div class="flex justify-center flex-wrap gap-4">
                            <button data-outcome="tersambung" class="dispo-btn px-5 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">Tersambung</button>
                            <button data-outcome="tidak_diangkat" class="dispo-btn px-5 py-2 text-sm font-medium text-white bg-yellow-500 rounded-md hover:bg-yellow-600">Tidak Diangkat</button>
                            <button data-outcome="nomor_sibuk" class="dispo-btn px-5 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">Nomor Sibuk</button>
                            <a href="{{ route('dialer.end') }}" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Akhiri Sesi</a>
                        </div>
                    </div>
                    <div id="loading-indicator" class="mt-4 text-gray-500 hidden">Memuat kontak berikutnya...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timerInterval;
        let seconds = 0;

        // Fungsi untuk mengupdate tampilan timer
        function updateTimerDisplay() {
            const minutes = Math.floor(seconds / 60).toString().padStart(2, '0');
            const remainingSeconds = (seconds % 60).toString().padStart(2, '0');
            document.getElementById('timer').innerText = `${minutes}:${remainingSeconds}`;
        }

        // Fungsi untuk memulai timer
        function startTimer() {
            clearInterval(timerInterval); // Hentikan timer sebelumnya jika ada
            seconds = 0;
            updateTimerDisplay();
            timerInterval = setInterval(() => {
                seconds++;
                updateTimerDisplay();
            }, 1000);
        }

        // Fungsi untuk memicu panggilan dan memulai timer
        function triggerCallAndStartTimer() {
            setTimeout(() => {
                document.getElementById('call-link').click();
                startTimer();
            }, 500);
        }
        
        // Fungsi untuk memicu panggilan, memulai timer, DAN UPDATE STATUS
        function triggerCallAndStartTimer() {
            setTimeout(() => {
                document.getElementById('call-link').click();

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

                startTimer();
            }, 500);
        }

        // Panggil fungsi saat halaman pertama kali dimuat
        window.onload = triggerCallAndStartTimer;

        // Tambahkan event listener ke semua tombol disposisi
        document.querySelectorAll('.dispo-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const outcome = this.dataset.outcome;
                const contactId = document.getElementById('dialer-card').dataset.contactId;
                const loadingIndicator = document.getElementById('loading-indicator');

                // Hentikan timer dan catat durasinya
                clearInterval(timerInterval);
                const duration = seconds;

                loadingIndicator.classList.remove('hidden');
                document.querySelectorAll('.dispo-btn').forEach(btn => btn.disabled = true);

                try {
                    // 1. Kirim laporan panggilan BESERTA DURASI
                    await fetch(`/api/dialer/log-report/${contactId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            outcome: outcome,
                            duration_seconds: duration // Kirim durasi
                        })
                    });

                    // 2. Ambil kontak berikutnya
                    const response = await fetch('/api/dialer/next-contact');
                    const nextContact = await response.json();

                    if (nextContact.session_ended) {
                        window.location.href = "{{ route('dialer.end') }}";
                        return;
                    }

                    // 3. Update tampilan dengan data kontak baru
                    document.getElementById('dialer-card').dataset.contactId = nextContact.id;
                    document.getElementById('contact-name').innerText = nextContact.name;
                    document.getElementById('contact-phone').innerText = nextContact.phone_number;
                    document.getElementById('contact-status').innerText = nextContact.status;
                    document.getElementById('call-link').href = `tel:${nextContact.phone_number}`;

                    // 4. Picu panggilan dan mulai timer baru
                    triggerCallAndStartTimer();

                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Sesi akan diakhiri.');
                    window.location.href = "{{ route('dialer.end') }}";
                } finally {
                    loadingIndicator.classList.add('hidden');
                    document.querySelectorAll('.dispo-btn').forEach(btn => btn.disabled = false);
                }
            });
        });
    </script>
</x-app-layout>
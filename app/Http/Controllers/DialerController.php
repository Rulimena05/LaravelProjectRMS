<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\CallLog;
use Illuminate\Http\Request;
use App\Models\Campaign;

class DialerController extends Controller
{
    // Method start(), view(), dan end() tidak berubah
    public function start(Request $request)
    {
        $user = auth()->user();
        $query = Contact::where('status', 'new')->orderBy('id', 'asc');

        // Filter berdasarkan campaign yang dipilih
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        } else {
            // Jika tidak ada campaign dipilih, hanya ambil dari campaign aktif atau yang tidak tercategory
            $query->where(function ($q) {
                $q->whereHas('campaign', fn($sub) => $sub->where('status', 'active'))
                    ->orWhereNull('campaign_id');
            });
        }

        // Filter berdasarkan agen jika bukan admin
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $contacts = $query->get();

        if ($contacts->isEmpty()) {
            return redirect()->route('contacts.index')->with('info', 'Tidak ada kontak baru untuk dihubungi di campaign ini.');
        }

        session(['dialer_queue' => $contacts->pluck('id')->toArray()]);
        session(['dialer_index' => 0]);
        return redirect()->route('dialer.view');
    }

    public function view()
    {
        if (!session()->has('dialer_queue')) {
            return redirect()->route('contacts.index')->with('info', 'Sesi dialing tidak aktif.');
        }

        $queue = session('dialer_queue');
        $index = session('dialer_index', 0);

        if ($index >= count($queue)) {
            return $this->end();
        }

        $contact_id = $queue[$index];
        $contact = Contact::find($contact_id);

        if (!$contact) {
            session()->increment('dialer_index');
            return redirect()->route('dialer.view');
        }

        $user = auth()->user();
        if (!$user->isAdmin() && $contact->user_id !== $user->id) {
            session()->increment('dialer_index');
            return redirect()->route('dialer.view');
        }

        return view('dialer.session', ['contact' => $contact]);
    }

    public function end()
    {
        session()->forget(['dialer_queue', 'dialer_index']);
        return redirect()->route('contacts.index')->with('success', 'Sesi dialing telah berakhir.');
    }


    // --- METHOD BARU UNTUK API ---

    /**
     * Menyimpan laporan panggilan via AJAX.
     */
    public function logReport(Request $request, Contact $contact)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $contact->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $request->validate([
            'outcome' => 'required|string',
            'duration_seconds' => 'required|integer|min:0', // Validasi durasi
        ]);

        CallLog::create([
            'contact_id' => $contact->id,
            'outcome' => $request->outcome,
            'user_id' => auth()->id(), // <-- TAMBAHKAN INI
            'duration_seconds' => $request->duration_seconds,
        ]);
        $contact->update(['status' => 'dihubungi']);

        // KEMBALIKAN STATUS KE ONLINE
        $user->update(['status' => 'online', 'status_updated_at' => now()]);

        return response()->json(['message' => 'Laporan berhasil disimpan.']);
    }

    /**
     * Mengambil data kontak berikutnya via AJAX.
     */
    public function getNextContact()
    {
        session()->increment('dialer_index');

        if (!session()->has('dialer_queue')) {
            return response()->json(['session_ended' => true]);
        }

        $queue = session('dialer_queue');
        $index = session('dialer_index', 0);

        if ($index >= count($queue)) {
            return response()->json(['session_ended' => true]);
        }

        $contact_id = $queue[$index];
        $contact = Contact::find($contact_id);

        if (!$contact) {
            // Jika kontak tidak ada, coba lagi kontak berikutnya secara rekursif
            return $this->getNextContact();
        }

        $user = auth()->user();
        if (!$user->isAdmin() && $contact->user_id !== $user->id) {
            // Jika kontak bukan milik agen, coba lagi kontak berikutnya
            return $this->getNextContact();
        }

        return response()->json($contact);
    }
}

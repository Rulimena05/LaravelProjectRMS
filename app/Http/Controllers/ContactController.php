<?php

namespace App\Http\Controllers; // <-- DIPERBAIKI: Menggunakan backslash '\'

use App\Models\Contact;
use App\Models\User;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\CallLog;
use App\Models\Callback;
use App\Models\PromiseToPay;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Contact::query()->with('campaign')
            ->where(function ($q) {
                $q->whereHas('campaign', fn($sub) => $sub->where('status', 'active'))
                    ->orWhereNull('campaign_id');
            });

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // --- LOGIKA PENCARIAN BARU ---
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('phone_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('task_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('additional_data->case_id', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        $contacts = $query->latest()->paginate(15);
        $campaigns = Campaign::all();

        return view('contacts.index', compact('contacts', 'campaigns'));
    }

    public function show(Contact $contact, Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $contact->user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK');
        }
        $contact->load('phoneNumbers');
        return view('contacts.show', [
            'contact' => $contact,
            'callback_id' => $request->query('callback_id')
        ]);
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:contacts',
            'email' => 'nullable|email|unique:contacts',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'new';
        $validated['user_id'] = null;
        Contact::create($validated);
        return redirect()->route('contacts.index')->with('success', 'Kontak baru berhasil ditambahkan.');
    }

    public function edit(Contact $contact)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $contact->user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK');
        }
        $agents = $user->isAdmin() ? User::where('role', 'agent')->get() : collect();
        return view('contacts.edit', [
            'contact' => $contact,
            'agents' => $agents,
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $contact->user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:contacts,phone_number,' . $contact->id,
            'email' => 'nullable|email|unique:contacts,email,' . $contact->id,
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);
        $contact->update($validated);
        return redirect()->route('contacts.index')->with('success', 'Data kontak berhasil diperbarui.');
    }

    public function destroy(Contact $contact)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'HANYA ADMIN YANG BISA MENGHAPUS KONTAK');
        }
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil dihapus.');
    }

    public function logCall(Request $request, Contact $contact)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $contact->user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'outcome' => 'required|string',
            'notes' => 'required|string',
            'duration_seconds' => 'required|integer|min:0',
            'new_phone_number' => 'nullable|string',
            'ptp_date' => 'required_if:outcome,PTP|nullable|date',
            'ptp_amount' => 'required_if:outcome,PTP|nullable|numeric',
        ]);

        // 1. Tambahkan nomor telepon baru jika ada
        if ($request->filled('new_phone_number')) {
            \App\Models\ContactPhoneNumber::create([
                'contact_id' => $contact->id,
                'label'      => 'Tambahan dari Agen',
                'number'     => $request->new_phone_number,
            ]);
        }

        // 2. Buat log panggilan
        $callLog = CallLog::create([
            'contact_id' => $contact->id,
            'user_id' => $user->id,
            'outcome' => $request->outcome,
            'notes' => $request->notes,
            'duration_seconds' => $request->duration_seconds,
        ]);

        // 3. Update status kontak dan buat PTP jika hasilnya PTP
        $newStatus = 'dihubungi';
        if ($request->outcome === 'PTP') {
            PromiseToPay::create([
                'call_log_id' => $callLog->id,
                'user_id' => $user->id,
                'contact_id' => $contact->id,
                'ptp_date' => $request->ptp_date,
                'ptp_amount' => $request->ptp_amount,
            ]);
            $newStatus = 'PTP';
        }
        $contact->update(['status' => $newStatus]);

        // 4. Kembalikan status agen ke 'online'
        $user->update(['status' => 'online', 'status_updated_at' => now()]);

        // 5. Redirect kembali ke halaman kontak yang sama
        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Laporan panggilan berhasil disimpan.');
    }

    public function nextContact(Contact $contact)
    {
        $user = auth()->user();

        $nextContactQuery = Contact::where('status', 'new')
            ->where('id', '>', $contact->id)
            // Tambahkan filter untuk campaign aktif
            ->where(function ($q) {
                $q->whereHas('campaign', fn($sub) => $sub->where('status', 'active'))
                    ->orWhereNull('campaign_id');
            })
            ->orderBy('id', 'asc');

        if (!$user->isAdmin()) {
            $nextContactQuery->where('user_id', $user->id);
        }

        $nextContact = $nextContactQuery->first();

        if ($nextContact) {
            return redirect()->route('contacts.show', $nextContact);
        }

        return redirect()->route('contacts.index')
            ->with('success', 'Semua kontak baru telah selesai dihubungi!');
    }
}

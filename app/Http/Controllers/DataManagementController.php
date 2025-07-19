<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Campaign;

class DataManagementController extends Controller
{
    /**
     * Menampilkan form upload.
     */
    public function create()
    {
        // Ambil semua campaign yang statusnya 'active'
        $campaigns = Campaign::where('status', 'active')->get();

        return view('data.upload', compact('campaigns'));
    }

    /**
     * Menyimpan data dari file yang diupload.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'campaign_id' => 'required|exists:campaigns,id', // Validasi campaign_id
        ]);

        try {
            // Kirim campaign_id ke class import
            Excel::import(new ContactsImport($request->campaign_id), $request->file('file'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi error saat import. Pastikan format file sudah benar. Error: ' . $e->getMessage());
        }

        return redirect()->route('contacts.index')->with('success', 'Data kontak berhasil diimpor!');
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/contacts_template.xlsx');
        return response()->download($filePath);
    }
}

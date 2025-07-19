<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\User;
use App\Models\ContactPhoneNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class ContactsImport implements ToModel, WithHeadingRow
{
    private $campaignId;

    // Daftar header untuk nomor telepon tambahan
    private $phoneColumns = [
        'phone_number_2', 'contact_phone_1', 'contact_phone_2',
        'contact_phone_3', 'contact_phone_4', 'contact_phone_5',
        'contact_phone_6', 'contact_phone_7', 'contact_phone_8'
    ];
    // Daftar header untuk data tambahan
    private $additionalDataColumns = [
        'product', 'date', 'case_id', 'gender', 'customer_occupation', 'dpd',
        'principle_outstanding', 'principal_overdue_curr', 'interest_overdue_curr',
        'last_late_fee', 'return_date', 'detail', 'loan_type', 'third_uid',
        'home_address', 'province', 'city', 'street', 'roomnumber', 'postcode',
        'assignment_date', 'withdrawal_date'
    ];

    public function __construct(int $campaignId)
    {
        $this->campaignId = $campaignId;
    }

    public function model(array $row)
    {
        if (empty($row['phone_number_1'])) return null;

        DB::transaction(function () use ($row) {
            $agent = !empty($row['agent']) ? User::where('name', 'like', $row['agent'])->first() : null;

            // Kumpulkan semua data tambahan ke dalam satu array
            $additionalData = [];
            foreach ($this->additionalDataColumns as $column) {
                if (!empty($row[$column])) {
                    $additionalData[$column] = $row[$column];
                }
            }
            
            // Buat kontak utama
            $contact = Contact::create([
                'task_id'      => $row['task_id'] ?? null,
                'name'         => $row['customer_name'] ?? 'Tanpa Nama',
                'phone_number' => $row['phone_number_1'],
                'email'        => $row['email'] ?? null,
                'status'       => 'new',
                'campaign_id'  => $this->campaignId,
                'user_id'      => $agent ? $agent->id : null,
                'additional_data' => $additionalData, // Simpan sebagai JSON
            ]);

            // Simpan semua nomor telepon tambahan
            foreach ($this->phoneColumns as $column) {
                if (!empty($row[$column])) {
                    ContactPhoneNumber::create([
                        'contact_id' => $contact->id,
                        'label'      => str_replace('_', ' ', $column), // "phone_number_2" -> "phone number 2"
                        'number'     => $row[$column],
                    ]);
                }
            }
        });

        // Kita return null karena pembuatan sudah ditangani di dalam transaction
        return null;
    }
}
<?php

namespace App\Exports;

use App\Models\CallLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CallLogsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $agentId;
    protected $startDate;
    protected $endDate;

    public function __construct($agentId, $startDate, $endDate)
    {
        $this->agentId = $agentId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = CallLog::query()->with(['user', 'contact', 'promiseToPay']);

        if ($this->agentId) {
            $query->where('user_id', $this->agentId);
        }
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }
        
        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'Tanggal Panggilan',
            'Agen',
            'Nama Kontak',
            'Nomor Telepon',
            'Task ID', // <-- Heading Baru
            'Case ID', // <-- Heading Baru
            'Hasil Panggilan',
            'Catatan',
            'Durasi (detik)',
            'Tanggal PTP',
            'Nominal PTP',
        ];
    }

    public function map($callLog): array
    {
        return [
            $callLog->created_at->format('Y-m-d H:i:s'),
            $callLog->user->name ?? 'N/A',
            $callLog->contact->name ?? 'N/A',
            $callLog->contact->phone_number ?? 'N/A',
            $callLog->contact->task_id ?? 'N/A', // <-- Data Baru
            $callLog->contact->additional_data['case_id'] ?? 'N/A', // <-- Data Baru
            $callLog->outcome,
            $callLog->notes,
            $callLog->duration_seconds,
            $callLog->promiseToPay ? $callLog->promiseToPay->ptp_date : '',
            $callLog->promiseToPay ? $callLog->promiseToPay->ptp_amount : '',
        ];
    }
}
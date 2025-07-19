<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\CallLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
{
    $agents = User::where('role', 'agent')->get();
    
    // Tambahkan with('promiseToPay') untuk memuat data PTP
    $query = CallLog::query()->with(['user', 'contact', 'promiseToPay']);

    if ($request->filled('agent_id')) {
        $query->where('user_id', $request->agent_id);
    }
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    $callLogs = $query->latest()->paginate(20);

    return view('reports.index', compact('callLogs', 'agents'));
}

    public function export(Request $request)
    {
        $agentId = $request->query('agent_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        return Excel::download(new CallLogsExport($agentId, $startDate, $endDate), 'laporan-panggilan.xlsx');
    }
}
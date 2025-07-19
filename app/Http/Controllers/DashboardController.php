<?php

namespace App\Http\Controllers; // <-- DIPERBAIKI: Menggunakan backslash '\'

use App\Models\Contact;
use App\Models\CallLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PromiseToPay;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Data untuk Kartu Statistik
        $totalContacts = $user->isAdmin() ? Contact::count() : Contact::where('user_id', $user->id)->count();
        
        $callsTodayQuery = CallLog::whereDate('created_at', today());
        if (!$user->isAdmin()) {
            $agentContactIds = Contact::where('user_id', $user->id)->pluck('id');
            $callsTodayQuery->whereIn('contact_id', $agentContactIds);
        }
        $totalCallsToday = (clone $callsTodayQuery)->count();
        $totalUniqueContactsCalledToday = (clone $callsTodayQuery)->distinct('contact_id')->count();
        
        // Data untuk Panggilan per Agen (hanya Admin)
        $callsPerAgentToday = collect();
        if ($user->isAdmin()) {
            $callsPerAgentToday = CallLog::whereDate('created_at', today())->with('user')
                ->select('user_id', DB::raw('count(*) as total'))
                ->groupBy('user_id')->get();
        }

        // Data untuk Grafik PTP per Agen (hanya Admin)
        $ptpLabels = collect();
        $ptpData = collect();
        if ($user->isAdmin()) {
            $ptpByAgent = PromiseToPay::with('user')
                ->select('user_id', DB::raw('sum(ptp_amount) as total_ptp'))
                ->groupBy('user_id')->get();
            $ptpLabels = $ptpByAgent->map(fn($item) => $item->user->name ?? 'N/A');
            $ptpData = $ptpByAgent->pluck('total_ptp');
        }

        // Data untuk Grafik Tren Panggilan Harian
        $dailyCallTrend = CallLog::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')->orderBy('date', 'asc')->get();
        $trendLabels = $dailyCallTrend->pluck('date');
        $trendData = $dailyCallTrend->pluck('total');
        
        return view('dashboard', compact(
            'totalContacts', 'totalCallsToday', 'totalUniqueContactsCalledToday', 'callsPerAgentToday',
            'ptpLabels', 'ptpData', 'trendLabels', 'trendData'
        ));
    }

    public function getAgentStatuses()
    {
        $agents = User::where('role', 'agent')->get();
        return response()->json($agents);
    }
}
<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DialerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DataManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\UserStatusController;
use Illuminate\Support\Facades\Route;
use App\Services\AsteriskService;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute CRM
    Route::resource('contacts', ContactController::class);
    Route::post('/contacts/{contact}/log', [ContactController::class, 'logCall'])->name('contacts.log_call');
    Route::get('/contacts/{contact}/next', [ContactController::class, 'nextContact'])->name('contacts.next');

    // Rute Dialer
    Route::controller(DialerController::class)->group(function () {
        Route::get('/dialer/start/{campaign?}', 'start')->name('dialer.start');
        Route::get('/dialer/session', 'view')->name('dialer.view');
        Route::post('/dialer/next', 'next')->name('dialer.next');
        Route::get('/dialer/end', 'end')->name('dialer.end');
    });
    Route::prefix('api/dialer')->name('api.dialer.')->group(function () {
        Route::get('/next-contact', [DialerController::class, 'getNextContact'])->name('next_contact');
        Route::post('/log-report/{contact}', [DialerController::class, 'logReport'])->name('log_report');
    });

    // Rute Broadcast
    Route::get('/broadcast', [BroadcastController::class, 'index'])->name('broadcast.index');

    // Rute Status Agen
    Route::post('/status/toggle-break', [UserStatusController::class, 'toggleBreak'])->name('status.toggle_break');
    Route::post('/status/update', [UserStatusController::class, 'update'])->name('status.update');

    // Rute khusus Admin
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/data/upload', [DataManagementController::class, 'create'])->name('data.create');
        Route::post('/data/upload', [DataManagementController::class, 'store'])->name('data.store');
        Route::get('/data/download-template', [DataManagementController::class, 'downloadTemplate'])->name('data.download_template');
        Route::resource('campaigns', CampaignController::class);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/api/kpi/agent-statuses', [DashboardController::class, 'getAgentStatuses'])->name('api.kpi.agent_statuses');
    });

    // Rute untuk mengirim panggilan melalui Asterisk
    Route::get('/test-call', function () {
        $asteriskService = new AsteriskService();

        // Definisikan DUA variabel
        $agentExtension = '101'; // Ganti dengan ekstensi agen yang benar
        $customerNumber = '081259352487'; // Ganti dengan nomor tujuan

        // Panggil fungsi dengan DUA argumen
        if ($asteriskService->originateCall($agentExtension, $customerNumber)) {
            return "Berhasil memulai panggilan ke agen {$agentExtension} untuk menyambungkan ke {$customerNumber}.";
        } else {
            return "Gagal memulai panggilan. Cek log server atau pastikan Asterisk berjalan.";
        }
    })->middleware('auth');
});

require __DIR__ . '/auth.php';

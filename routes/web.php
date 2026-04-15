<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForm'])->name('password.forgot');
    Route::post('/forgot-password/check-email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'checkEmail'])->name('password.check-email');
    Route::post('/forgot-password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware(['auth'])
        ->name('dashboard');

    // Cases (Digital Blotter)
    Route::resource('cases', App\Http\Controllers\LuponCaseController::class)->only(['index', 'create', 'show', 'edit']);

    // Hearings
    Route::resource('hearings', App\Http\Controllers\HearingController::class)->only(['index', 'create', 'show']);

    // Folderized Reports (MOV 2)
    Route::get('folderized-reports', [App\Http\Controllers\FolderizedReportController::class, 'index'])->name('folderized-reports.index');

    // Reports (Form 1)
    Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{type}', [App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');

    // Settings & Personnel Management
    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/security', [App\Http\Controllers\SettingController::class, 'updateSecurity'])->name('settings.security');
    Route::post('settings/maintenance', [App\Http\Controllers\SettingController::class, 'maintenance'])->name('settings.maintenance');
    Route::get('settings/maintenance/download', [App\Http\Controllers\SettingController::class, 'maintenance'])->name('settings.maintenance.download');
    Route::post('settings/maintenance/restore', [App\Http\Controllers\SettingController::class, 'maintenance'])->name('settings.maintenance.restore');

    // Hidden Branding (Unlock and Update)
    Route::get('settings/unlock-branding', [App\Http\Controllers\SettingController::class, 'unlockBranding'])->name('settings.unlock-branding');
    Route::post('settings/branding', [App\Http\Controllers\SettingController::class, 'updateBranding'])->name('settings.update-branding');

    // Lupon Members CRUD
    Route::post('settings/members', [App\Http\Controllers\SettingController::class, 'storeMember'])->name('settings.members.store');
    Route::put('settings/members/{member}', [App\Http\Controllers\SettingController::class, 'updateMember'])->name('settings.members.update');
    Route::delete('settings/members/{member}', [App\Http\Controllers\SettingController::class, 'destroyMember'])->name('settings.members.destroy');
    Route::post('settings/members/{member}/toggle', [App\Http\Controllers\SettingController::class, 'toggleMemberStatus'])->name('settings.members.toggle');

    // Citizen Registry
    Route::resource('citizens', App\Http\Controllers\CitizenController::class);

    // KP Document Exports
    Route::prefix('cases/{case}/export')->name('cases.export.')->group(function () {
        Route::get('notice-of-hearing', [App\Http\Controllers\DocumentExportController::class, 'noticeOfHearing'])->name('notice-of-hearing');
        Route::get('summon', [App\Http\Controllers\DocumentExportController::class, 'summon'])->name('summon');
        Route::get('invitation-notice', [App\Http\Controllers\DocumentExportController::class, 'invitationNotice'])->name('invitation-notice');
        Route::get('amicable-settlement', [App\Http\Controllers\DocumentExportController::class, 'amicableSettlement'])->name('amicable-settlement');
        Route::get('kasabutan', [App\Http\Controllers\DocumentExportController::class, 'kasabutan'])->name('kasabutan');
        Route::get('certificate-to-file-action', [App\Http\Controllers\DocumentExportController::class, 'certificateToFileAction'])->name('certificate-to-file-action');
        Route::get('status-of-case', [App\Http\Controllers\DocumentExportController::class, 'statusOfCase'])->name('status-of-case');
        Route::get('endorsement', [App\Http\Controllers\DocumentExportController::class, 'endorsement'])->name('endorsement');
        Route::post('save-content', [App\Http\Controllers\DocumentExportController::class, 'saveContent'])->name('save-content');
        Route::post('update-from-document', [App\Http\Controllers\LuponCaseController::class, 'updateFromDocument'])->name('update-from-document');
        Route::post('toggle-step-completion', [App\Http\Controllers\LuponCaseController::class, 'toggleStepCompletion'])->name('toggle-step-completion');
    });

    // Reports persistence & finalization
    Route::post('reports/save-content', [App\Http\Controllers\ReportController::class, 'saveContent'])->name('reports.save-content');
    Route::post('reports/finalize', [App\Http\Controllers\ReportController::class, 'finalize'])->name('reports.finalize');
    Route::get('reports/print/{report}', [App\Http\Controllers\ReportController::class, 'printReport'])->name('reports.print');
    Route::get('reports/print-template/{name}', [App\Http\Controllers\ReportController::class, 'printTemplate'])->name('reports.print-template');

    // Media Proxy (Bypasses storage symlink for .exe portability)
    Route::get('media/{path}', [\App\Http\Controllers\MediaController::class, 'show'])
        ->where('path', '.*')
        ->name('media.show');
});

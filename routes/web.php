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
    Route::resource('cases', App\Http\Controllers\LuponCaseController::class)->only(['index', 'create', 'show', 'edit', 'destroy']);
    Route::post('cases/{case}/update-status', [App\Http\Controllers\LuponCaseController::class, 'updateStatus'])->name('cases.update-status');

    // Case Document Uploads (Version 2)
    Route::post('cases/{case}/documents', [App\Http\Controllers\DocumentUploadController::class, 'store'])->name('cases.documents.store');
    Route::delete('cases/{case}/documents/{document}', [App\Http\Controllers\DocumentUploadController::class, 'destroy'])->name('cases.documents.destroy');
    Route::get('cases/{case}/documents/{document}/download', [App\Http\Controllers\DocumentUploadController::class, 'download'])->name('cases.documents.download');
    Route::get('cases/{case}/documents/{document}/open', [App\Http\Controllers\DocumentUploadController::class, 'open'])->name('cases.documents.open');
    Route::get('documents/{document}/view-pdf', [App\Http\Controllers\PDFViewerController::class, 'viewDocument'])->name('pdf-viewer.document');
    Route::get('documents/{document}/stream-pdf', [App\Http\Controllers\PDFViewerController::class, 'streamDocument'])->name('pdf-viewer.stream-document');

    // Hearings
    Route::resource('hearings', App\Http\Controllers\HearingController::class)->only(['index', 'create', 'show']);

    // Folderized Reports (MOV 2)
    Route::get('folderized-reports', [App\Http\Controllers\FolderizedReportController::class, 'index'])->name('folderized-reports.index');
    Route::get('folderized-reports/older', [App\Http\Controllers\FolderizedReportController::class, 'fetchOlder'])->name('folderized-reports.older');

    // Reports (Form 1) — index (Dashboard)
    Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    // Monthly Reports Uploads (Version 2)
    Route::post('reports/upload', [App\Http\Controllers\ReportUploadController::class, 'store'])->name('reports.upload');
    Route::delete('reports/{report}', [App\Http\Controllers\ReportUploadController::class, 'destroy'])->name('reports.destroy');
    Route::post('reports/{report}/replace', [App\Http\Controllers\ReportUploadController::class, 'replace'])->name('reports.replace');
    Route::get('reports/{report}/download', [App\Http\Controllers\ReportUploadController::class, 'download'])->name('reports.download');
    Route::get('reports/{report}/open', [App\Http\Controllers\ReportUploadController::class, 'open'])->name('reports.open');
    Route::get('reports/{report}/view-pdf', [App\Http\Controllers\PDFViewerController::class, 'viewReport'])->name('pdf-viewer.report');
    Route::get('reports/{report}/stream-pdf', [App\Http\Controllers\PDFViewerController::class, 'streamReport'])->name('pdf-viewer.stream-report');

    // Settings & Personnel Management
    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/security', [App\Http\Controllers\SettingController::class, 'updateSecurity'])->name('settings.security');
    Route::post('settings/maintenance', [App\Http\Controllers\SettingController::class, 'maintenance'])->name('settings.maintenance');
    Route::get('settings/maintenance/download', [App\Http\Controllers\SettingController::class, 'maintenance'])->name('settings.maintenance.download');
    Route::post('settings/maintenance/restore', [App\Http\Controllers\SettingController::class, 'maintenance'])->name('settings.maintenance.restore');

    // Lupon Members CRUD
    Route::post('settings/members', [App\Http\Controllers\SettingController::class, 'storeMember'])->name('settings.members.store');
    Route::put('settings/members/{member}', [App\Http\Controllers\SettingController::class, 'updateMember'])->name('settings.members.update');
    Route::delete('settings/members/{member}', [App\Http\Controllers\SettingController::class, 'destroyMember'])->name('settings.members.destroy');
    Route::post('settings/members/{member}/toggle', [App\Http\Controllers\SettingController::class, 'toggleMemberStatus'])->name('settings.members.toggle');

    // Citizen Registry
    Route::resource('citizens', App\Http\Controllers\CitizenController::class);

    // Media Proxy (Bypasses storage symlink for .exe portability)
    Route::get('media/{path}', [\App\Http\Controllers\MediaController::class, 'show'])
        ->where('path', '.*')
        ->name('media.show');
});

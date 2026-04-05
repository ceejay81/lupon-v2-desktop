<?php

namespace App\Http\Controllers;

use App\Models\LuponMember;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $user = auth()->user();
        $members = \App\Models\LuponMember::orderBy('name')->get();

        // Real backups from storage
        $backupPath = storage_path('app/backups');
        $backups = [];
        if (is_dir($backupPath)) {
            $files = glob($backupPath.'/*.sqlite');
            rsort($files);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => basename($file),
                    'size' => round(filesize($file) / 1024, 1).' KB',
                    'date' => date('Y-m-d H:i', filemtime($file)),
                ];
            }
        }

        $logs = [];

        return view('settings.index', compact('settings', 'user', 'members', 'backups', 'logs'));
    }

    /**
     * Update user security settings (Email & Password).
     */
    public function updateSecurity(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        if ($request->filled('email')) {
            $request->validate([
                'email' => 'required|email|unique:users,email,'.$user->id,
            ]);
            $user->update(['email' => $request->email]);

            return redirect()->route('settings.index', ['#security'])->with('message', 'Email updated successfully.');
        }

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:6|confirmed',
            ]);
            $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);

            return redirect()->route('settings.index', ['#security'])->with('message', 'Password updated successfully.');
        }

        return redirect()->route('settings.index');
    }

    /**
     * Handle maintenance tasks (Backup & Restore).
     */
    public function maintenance(Request $request)
    {
        $action = $request->input('action');

        try {
            if ($action === 'backup') {
                $dbPath = database_path('database.sqlite');
                $filename = 'lupon_backup_'.now()->format('Y-m-d_H-i-s').'.sqlite';
                $dest = storage_path('app/backups/'.$filename);

                if (! is_dir(storage_path('app/backups'))) {
                    mkdir(storage_path('app/backups'), 0755, true);
                }

                if (! file_exists($dbPath)) {
                    return redirect()->route('settings.index')->with('error', 'Database file not found.');
                }

                if (! @copy($dbPath, $dest)) {
                    throw new \Exception('Failed to copy database file.');
                }

                return redirect()->route('settings.index')->with('message', "Backup \"{$filename}\" created successfully.");
            }

            if ($action === 'download') {
                $filename = $request->input('filename');
                $path = storage_path('app/backups/'.$filename);

                if (! file_exists($path) || ! str_ends_with($filename, '.sqlite')) {
                    abort(404);
                }

                return response()->download($path);
            }

            if ($action === 'restore') {
                $request->validate([
                    'backup_file' => 'required|file|max:51200',
                ]);

                $dbPath = database_path('database.sqlite');
                
                // 1. Safety Copy
                $safetyPath = storage_path('app/backups/pre_restore_'.now()->format('Y-m-d_H-i-s').'.sqlite');
                if (! is_dir(storage_path('app/backups'))) {
                    mkdir(storage_path('app/backups'), 0755, true);
                }
                
                if (! @copy($dbPath, $safetyPath)) {
                    throw new \Exception('Failed to create safety backup before restore.');
                }

                // 2. Disconnect and Flush DB before overwrite
                \Illuminate\Support\Facades\DB::disconnect();
                
                // 3. Give Windows a small 200ms window to release file handles
                usleep(200000);

                // 4. Forceful overwrite using file_put_contents
                $success = @file_put_contents(
                    $dbPath, 
                    file_get_contents($request->file('backup_file')->getRealPath()),
                    LOCK_EX
                );

                if ($success === false) {
                    throw new \Exception('Restoration failed: The database file is currently locked by another process (likely php artisan serve). Please stop the server and try again.');
                }

                return redirect()->route('settings.index')->with('message', 'Database restored successfully. A safety backup of the previous database was saved automatically.');
            }

            if ($action === 'delete') {
                $filename = $request->input('filename');
                $path = storage_path('app/backups/'.$filename);

                if (file_exists($path) && str_ends_with($filename, '.sqlite')) {
                    unlink($path);
                    return redirect()->route('settings.index', ['#maintenance'])->with('warning', "Backup file \"{$filename}\" deleted successfully.");
                }

                return redirect()->route('settings.index', ['#maintenance'])->with('error', 'File not found or invalid.');
            }

            if ($action === 'batch_delete') {
                $filenames = $request->input('filenames', []);
                $deletedCount = 0;
                
                if (empty($filenames)) {
                    return redirect()->route('settings.index', ['#maintenance'])->with('error', 'No files selected for deletion.');
                }

                foreach ($filenames as $filename) {
                    $path = storage_path('app/backups/'.$filename);
                    if (file_exists($path) && str_ends_with($filename, '.sqlite')) {
                        @unlink($path);
                        $deletedCount++;
                    }
                }
                
                return redirect()->route('settings.index', ['#maintenance'])->with('warning', "$deletedCount backup files removed successfully.");
            }
        } catch (\Exception $e) {
            return redirect()->route('settings.index', ['#maintenance'])->with('error', 'System Maintenance Error: '.$e->getMessage());
        }

        return redirect()->route('settings.index', ['#maintenance']);
    }

    /**
     * Store a new Lupon Member.
     */
    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'member_type' => 'required|in:punong_barangay,lupon_secretary,regular',
            'appointment_date' => 'nullable|date',
        ]);

        LuponMember::create($validated);

        return redirect()->route('settings.index')->with('message', 'Lupon Member added successfully.');
    }

    /**
     * Update a Lupon Member.
     */
    public function updateMember(Request $request, LuponMember $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'member_type' => 'required|in:punong_barangay,lupon_secretary,regular',
            'appointment_date' => 'nullable|date',
        ]);

        $member->update($validated);

        return redirect()->route('settings.index')->with('message', 'Lupon Member updated successfully.');
    }

    /**
     * Toggle member active status.
     */
    public function toggleMemberStatus(LuponMember $member)
    {
        $member->update(['is_active' => ! $member->is_active]);
        $status = $member->is_active ? 'activated' : 'deactivated';

        return redirect()->route('settings.index')->with('message', "Member status $status successfully.");
    }

    /**
     * Delete a Lupon Member.
     */
    public function destroyMember(LuponMember $member)
    {
        $member->delete();

        return redirect()->route('settings.index')->with('warning', 'Lupon Member removed successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')->with('message', 'System parameters updated successfully.');
    }

    /**
     * Secretly unlock the branding feature.
     */
    public function unlockBranding()
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'is_branding_enabled'],
            ['value' => 'true', 'type' => 'boolean', 'group' => 'branding']
        );

        return redirect()->route('settings.index', ['#branding'])->with('message', 'Premium Branding Features Unlocked.');
    }

    /**
     * Update the Barangay Logo.
     */
    public function updateBranding(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'barangay_logo_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Ensure the directory exists
            if (!is_dir(public_path('images/branding'))) {
                mkdir(public_path('images/branding'), 0755, true);
            }

            $file->move(public_path('images/branding'), $filename);

            \App\Models\Setting::updateOrCreate(
                ['key' => 'barangay_logo_path'],
                ['value' => 'images/branding/' . $filename, 'type' => 'string', 'group' => 'branding']
            );

            return redirect()->route('settings.index', ['#branding'])->with('message', 'Barangay Logo updated successfully.');
        }

        return redirect()->route('settings.index')->with('error', 'Failed to upload logo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}

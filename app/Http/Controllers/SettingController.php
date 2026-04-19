<?php

namespace App\Http\Controllers;

use App\Models\LuponMember;
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
            $files = glob($backupPath.'/*.zip');
            rsort($files);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => basename($file),
                    'size' => round(filesize($file) / 1024 / 1024, 2).' MB',
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
            // Safety Check: Ensure we are on SQLite
            if (config('database.default') !== 'sqlite') {
                throw new \Exception('Backup System Error: The application is currently using '.config('database.default').'. This backup engine is specifically designed for SQLite offline mode. Please update your .env file to DB_CONNECTION=sqlite.');
            }

            if ($action === 'backup') {
                $dbPath = database_path('database.sqlite');
                $timestamp = now()->format('Y-m-d_H-i-s');
                $filename = 'lupon_backup_'.$timestamp.'.zip';
                $dest = storage_path('app/backups/'.$filename);

                if (! is_dir(storage_path('app/backups'))) {
                    mkdir(storage_path('app/backups'), 0755, true);
                }

                if (! file_exists($dbPath)) {
                    return redirect()->route('settings.index')->with('error', 'Database file not found.');
                }

                // 1. Create a safe Database Snapshot (Non-Blocking)
                $tempDb = storage_path('app/backups/temp_db_'.$timestamp.'.sqlite');
                try {
                    // Use VACUUM INTO for a consistent, compacted snapshot
                    // This works via PDO and doesn't require the sqlite3 extension
                    \Illuminate\Support\Facades\DB::statement('VACUUM INTO ? ', [$tempDb]);
                } catch (\Exception $e) {
                    throw new \Exception('Failed to create database snapshot: '.$e->getMessage());
                }

                // 2. Bundle into ZIP
                $zip = new \ZipArchive;
                if ($zip->open($dest, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                    // Add Database
                    $zip->addFile($tempDb, 'database.sqlite');

                    // Add Storage Public (Photos/Attachments)
                    if (is_dir(storage_path('app/public'))) {
                        $files = new \RecursiveIteratorIterator(
                            new \RecursiveDirectoryIterator(storage_path('app/public')),
                            \RecursiveIteratorIterator::LEAVES_ONLY
                        );
                        foreach ($files as $name => $file) {
                            if (! $file->isDir()) {
                                $filePath = $file->getRealPath();
                                $relativePath = 'storage/'.substr($filePath, strlen(storage_path('app/public')) + 1);
                                $zip->addFile($filePath, $relativePath);
                            }
                        }
                    }

                    // Note: Branding images are now saved in storage/app/public/branding
                    // so they are automatically included in the Storage logic above.

                    $zip->close();
                } else {
                    throw new \Exception('Failed to create backup ZIP file.');
                }

                // Cleanup temp DB
                @unlink($tempDb);

                return redirect()->route('settings.index')->with('message', "Backup created successfully. Filename: {$filename}")->with('backup_success', true)->with('latest_backup', $filename);
            }

            if ($action === 'download') {
                $filename = $request->input('filename');
                $path = storage_path('app/backups/'.$filename);

                if (! file_exists($path) || ! str_ends_with($filename, '.zip')) {
                    abort(404);
                }

                return response()->download($path);
            }

            if ($action === 'restore') {
                $request->validate([
                    'backup_file' => 'required|file|max:102400', // 100MB
                ]);

                $zipFile = $request->file('backup_file');
                $extractPath = storage_path('app/temp_restore_'.now()->timestamp);

                // 1. Extraction and Validation
                $zip = new \ZipArchive;
                if ($zip->open($zipFile->getRealPath()) !== true) {
                    throw new \Exception('Failed to open backup ZIP file.');
                }

                if (! is_dir($extractPath)) {
                    mkdir($extractPath, 0755, true);
                }
                $zip->extractTo($extractPath);
                $zip->close();

                $restoredDb = $extractPath.'/database.sqlite';
                if (! file_exists($restoredDb)) {
                    throw new \Exception('Invalid backup: database.sqlite missing from ZIP.');
                }

                // 2. Integrity Check
                try {
                    // Create a temporary connection to check the extracted DB
                    config(['database.connections.temp_restore' => [
                        'driver' => 'sqlite',
                        'database' => $restoredDb,
                        'prefix' => '',
                    ]]);

                    $result = \Illuminate\Support\Facades\DB::connection('temp_restore')->selectOne('PRAGMA integrity_check');
                    \Illuminate\Support\Facades\DB::purge('temp_restore');

                    if (! $result || $result->integrity_check !== 'ok') {
                        throw new \Exception('Database integrity check failed: '.($result->integrity_check ?? 'Unknown error'));
                    }
                } catch (\Exception $e) {
                    throw new \Exception('Could not verify database integrity: '.$e->getMessage());
                }

                // 3. Emergency Snapshot (Rollback)
                $dbPath = database_path('database.sqlite');
                $safetyZip = storage_path('app/backups/EMERGENCY_ROLLBACK_'.now()->format('Y-m-d_H-i-s').'.zip');

                $safety = new \ZipArchive;
                if ($safety->open($safetyZip, \ZipArchive::CREATE) === true) {
                    $safety->addFile($dbPath, 'database.sqlite');
                    // Add existing media/branding if possible, but prioritize DB for rollback speed
                    $safety->close();
                }

                // 4. Atomic Swap (Windows Compatible)
                \Illuminate\Support\Facades\DB::disconnect();
                usleep(500000); // 0.5s for file locks

                try {
                    // Files to swap
                    $targets = [
                        'database' => [
                            'active' => $dbPath,
                            'new' => $restoredDb,
                            'wal' => $dbPath.'-wal',
                            'shm' => $dbPath.'-shm',
                        ],
                        'storage' => [
                            'active' => storage_path('app/public'),
                            'new' => $extractPath.'/storage',
                        ],
                        'branding' => [ // Kept for backwards compatibility with legacy backups
                            'active' => storage_path('app/public/branding'),
                            'new' => $extractPath.'/branding',
                        ],
                    ];

                    // Process DB Swap
                    if (file_exists($dbPath)) {
                        $toDelete = $dbPath.'.to_delete_'.now()->timestamp;
                        if (! @rename($dbPath, $toDelete)) {
                            throw new \Exception('System Error: Could not rename active database. Ensure no other applications are using it.');
                        }
                    }

                    if (! @copy($restoredDb, $dbPath)) {
                        throw new \Exception('System Error: Failed to move new database into place.');
                    }

                    // Delete WAL/SHM to prevent corruption
                    if (file_exists($targets['database']['wal'])) {
                        @unlink($targets['database']['wal']);
                    }
                    if (file_exists($targets['database']['shm'])) {
                        @unlink($targets['database']['shm']);
                    }

                    // Process Media/Branding Swaps
                    foreach (['storage', 'branding'] as $key) {
                        if (is_dir($targets[$key]['new'])) {
                            // Minimalist swap for media: just copy new over old if possible
                            // In a full implementation, we'd do a recursive move
                            $this->recursiveCopy($targets[$key]['new'], $targets[$key]['active']);
                        }
                    }

                } catch (\Exception $e) {
                    throw $e;
                } finally {
                    // Cleanup temp folder
                    $this->deleteDirectory($extractPath);
                    if (isset($toDelete) && file_exists($toDelete)) {
                        @unlink($toDelete);
                    }
                }

                return redirect()->route('settings.index')->with('message', 'System state restored successfully. An emergency rollback ZIP was created in your backups folder.');
            }

            if ($action === 'delete') {
                $filename = $request->input('filename');
                $path = storage_path('app/backups/'.$filename);

                if (file_exists($path) && str_ends_with($filename, '.zip')) {
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
                    if (file_exists($path) && str_ends_with($filename, '.zip')) {
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
     * Secretly unlock the branding feature with a specific code.
     */
    public function unlockBranding(Request $request)
    {
        $code = $request->input('code');

        if (strtoupper($code) !== 'LUPON-PREMIUM-2026') {
            return redirect()->route('settings.index', ['#maintenance'])->with('error', 'Invalid feature unlock code.');
        }

        \App\Models\Setting::updateOrCreate(
            ['key' => 'is_branding_enabled'],
            ['value' => 'true', 'type' => 'boolean', 'group' => 'branding']
        );

        return redirect()->route('settings.index', ['#branding'])->with('message', 'Premium Branding Features Unlocked.');
    }

    /**
     * Update the Barangay Logo.
     */
    /**
     * Secretly lock the branding feature.
     */
    public function lockBranding()
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'is_branding_enabled'],
            ['value' => 'false', 'type' => 'boolean', 'group' => 'branding']
        );

        return redirect()->route('settings.index')->with('message', 'Premium Branding Features Locked.');
    }

    public function updateBranding(Request $request)
    {
        // Backend Safety Check
        if (! \App\Models\Setting::get('is_branding_enabled')) {
            return redirect()->route('settings.index')->with('error', 'Branding features are currently locked.');
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'barangay_logo_'.time().'.'.$file->getClientOriginalExtension();

            // Ensure the directory exists targeting AppData mapped storage
            if (! is_dir(storage_path('app/public/branding'))) {
                mkdir(storage_path('app/public/branding'), 0755, true);
            }

            $file->move(storage_path('app/public/branding'), $filename);

            \App\Models\Setting::updateOrCreate(
                ['key' => 'barangay_logo_path'],
                ['value' => 'media/branding/'.$filename, 'type' => 'string', 'group' => 'branding']
            );

            return redirect()->route('settings.index', ['#branding'])->with('message', 'Barangay Logo updated successfully.');
        }

        return redirect()->route('settings.index')->with('error', 'Failed to upload logo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Helper to delete a directory and its contents recursively.
     */
    private function deleteDirectory($dir)
    {
        if (! is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    /**
     * Helper to copy a directory and its contents recursively.
     */
    private function recursiveCopy($src, $dst)
    {
        if (! is_dir($src)) {
            return;
        }
        if (! is_dir($dst)) {
            mkdir($dst, 0755, true);
        }
        $files = array_diff(scandir($src), ['.', '..']);
        foreach ($files as $file) {
            if (is_dir("$src/$file")) {
                $this->recursiveCopy("$src/$file", "$dst/$file");
            } else {
                copy("$src/$file", "$dst/$file");
            }
        }
    }
}

@extends('layouts.app')

@section('title', 'Admin Settings | Lupon')
@section('page-title', 'Admin Settings')

@section('content')
<div class="settings-container">

    {{-- Tab Navigation --}}
    <nav class="settings-tabs">
        <button class="settings-tab-btn active" data-tab="personnel" onclick="switchTab('personnel')">
            <i class="ph ph-users-three"></i> Personnel Management
        </button>
        <button class="settings-tab-btn" data-tab="security" onclick="switchTab('security')">
            <i class="ph ph-shield-check"></i> Security
        </button>
        <button class="settings-tab-btn" data-tab="maintenance" onclick="switchTab('maintenance')">
            <i class="ph ph-database"></i> System Maintenance
        </button>

        {{-- Secret Branding Tab --}}
        @if(\App\Models\Setting::get('is_branding_enabled'))
        <button class="settings-tab-btn" data-tab="branding" onclick="switchTab('branding')">
            <i class="ph ph-paint-brush"></i> Premium Branding
        </button>
        @endif
    </nav>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB 1: Personnel Management                        --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div class="settings-tab-panel active" id="tab-personnel" x-data="{ confirmDelete: false, deleteUrl: '', deleteName: '' }">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="ph ph-users-three"></i> Lupon Members</h2>
                <button class="btn-primary btn-sm" onclick="openMemberModal()">
                    <i class="ph ph-plus"></i> Register Member
                </button>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="settings-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Type</th>
                            <th>Appointed</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-primary);">{{ $member->name }}</td>
                            <td>{{ $member->position }}</td>
                            <td>
                                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: capitalize;">
                                    {{ str_replace('_', ' ', $member->member_type) }}
                                </span>
                            </td>
                            <td>{{ $member->appointment_date ? $member->appointment_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $member->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <button class="btn-sm" title="Edit Member"
                                        onclick="openMemberModal({
                                            id: {{ $member->id }},
                                            name: '{{ addslashes($member->name) }}',
                                            position: '{{ addslashes($member->position) }}',
                                            member_type: '{{ $member->member_type }}',
                                            appointment_date: '{{ $member->appointment_date ? $member->appointment_date->format('Y-m-d') : '' }}'
                                        })">
                                        <i class="ph ph-pencil"></i>
                                    </button>
                                    <form action="{{ route('settings.members.toggle', $member) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-sm" title="{{ $member->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="ph {{ $member->is_active ? 'ph-user-minus' : 'ph-user-plus' }}"></i>
                                        </button>
                                    </form>
                                    
                                    <button type="button" class="btn-sm" style="color: var(--danger);" title="Delete"
                                            @click="confirmDelete = true; deleteUrl = '{{ route('settings.members.destroy', $member) }}'; deleteName = '{{ addslashes($member->name) }}'">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <i class="ph ph-users-three" style="font-size: 2.5rem; opacity: 0.3; display: block; margin-bottom: 8px;"></i>
                                No Lupon members registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Member Deletion Modal --}}
        <template x-if="confirmDelete">
            <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
                <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 420px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                    <div style="padding: 1.75rem 2rem;">
                        <div style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                            <i class="ph ph-user-minus" style="font-size: 1.5rem; color: var(--danger);"></i>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Remove Member?</h3>
                        <p style="font-size: 0.875rem; color: var(--text-secondary);">Are you sure you want to remove <strong x-text="deleteName"></strong>? This action cannot be undone.</p>
                    </div>
                    <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                        <button @click="confirmDelete = false" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                            Cancel
                        </button>
                        <form :action="deleteUrl" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--danger); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="ph ph-trash"></i> Yes, Remove Permanently
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB 2: Security                                    --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div class="settings-tab-panel" id="tab-security" style="display:none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

            {{-- Update Email --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="ph ph-envelope"></i> Email Address</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.security') }}" method="POST">
                        @csrf
                        <div class="settings-field">
                            <label>Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="settings-input">
                            @error('email') <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                        <div style="margin-top: 1.25rem;">
                            <button type="submit" class="btn-primary">Update Email</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="ph ph-lock-key"></i> Change Password</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.security') }}" method="POST">
                        @csrf
                        <div class="settings-field">
                            <label>New Password</label>
                            <input type="password" name="password" class="settings-input" placeholder="Minimum 6 characters">
                            @error('password') <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                        <div class="settings-field" style="margin-top: 0.75rem;">
                            <label>Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="settings-input">
                        </div>
                        <div style="margin-top: 1.25rem;">
                            <button type="submit" class="btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB 3: System Maintenance                          --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div class="settings-tab-panel" id="tab-maintenance" style="display:none;">
        {{-- Backup Hero Card --}}
        <div class="settings-backup-hero" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid #334155; position: relative; overflow: hidden;">
            {{-- Decorative Background Icons --}}
            <i class="ph ph-usb" style="position: absolute; right: -20px; top: -20px; font-size: 8rem; color: rgba(255,255,255,0.03); transform: rotate(15deg);"></i>
            <i class="ph ph-google-drive-logo" style="position: absolute; right: 80px; bottom: -20px; font-size: 6rem; color: rgba(255,255,255,0.03); transform: rotate(-10deg);"></i>

            <div style="position: relative; z-index: 1;">
                <h3 style="font-size: 0.7rem; font-weight: 800; color: var(--accent-blue); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">
                    <i class="ph ph-shield-check"></i> System Protection
                </h3>
                <div style="font-size: 1.75rem; font-weight: 800; color: white; margin: 0 0 8px 0; letter-spacing: -0.02em;">Data Maintenance</div>
                <p style="font-size: 0.875rem; color: #94a3b8; max-width: 480px; line-height: 1.6;">
                    Creates a complete system snapshot (Database + Photos) bundled into a secure ZIP file for your records.
                </p>
                <div style="display: flex; gap: 16px; margin-top: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 0.75rem; font-weight: 600;">
                        <i class="ph ph-usb" style="font-size: 1.125rem;"></i> USB Safe
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 0.75rem; font-weight: 600;">
                        <i class="ph ph-google-drive-logo" style="font-size: 1.125rem;"></i> Cloud Ready
                    </div>
                </div>
            </div>
            <form action="{{ route('settings.maintenance') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="backup">
                <button type="submit" class="btn-primary" style="background: var(--success); padding: 12px 24px; display: flex; align-items: center; gap: 8px;">
                    <i class="ph ph-cloud-arrow-down" style="font-size: 1.25rem;"></i> Create Backup Now
                </button>
            </form>
        </div>

        {{-- Restore Card --}}
        <div class="card" style="margin-top: 1.5rem; border-left: 4px solid var(--warning);" x-data="{ confirmRestore: false }">
            <div class="card-header">
                <h2 class="card-title"><i class="ph ph-upload-simple" style="color: var(--warning);"></i> Restore from Backup</h2>
            </div>
            <div class="card-body">
                <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                    Upload a previously created <code>.zip</code> backup file to restore the database and system media.
                    An emergency rollback snapshot is created automatically before any changes are made.
                </p>
                <form id="restore-form" action="{{ route('settings.maintenance.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="restore">
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <input type="file" name="backup_file" accept=".zip"
                               class="form-control" style="max-width: 360px;" required
                               x-ref="fileInput">
                        <button type="button" class="btn-primary"
                                style="background: var(--warning); border-color: var(--warning); white-space: nowrap;"
                                @click="if ($refs.fileInput.files.length) confirmRestore = true; else $refs.fileInput.reportValidity()">
                            <i class="ph ph-arrow-counter-clockwise"></i> Restore Database
                        </button>
                    </div>
                    @error('backup_file') <p class="form-error" style="margin-top: 0.5rem;"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                </form>
            </div>

            {{-- Confirm Modal --}}
            <template x-if="confirmRestore">
                <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
                    <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 420px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                        <div style="padding: 1.75rem 2rem;">
                            <div style="width: 48px; height: 48px; background: var(--warning-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                                <i class="ph ph-warning" style="font-size: 1.5rem; color: var(--warning);"></i>
                            </div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Perform Full System Restore?</h3>
                            <p style="font-size: 0.875rem; color: var(--text-secondary);">This will replace the current database and all media files with the content of the backup. An **Emergency Rollback** will be saved automatically.</p>
                        </div>
                        <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                            <button @click="confirmRestore = false" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                                Cancel
                            </button>
                            <button @click="confirmRestore = false; document.getElementById('restore-form').submit()" type="button"
                                style="height: 40px; padding: 0 1.25rem; background: var(--warning); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="ph ph-arrow-counter-clockwise"></i> Yes, Restore
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Backup History --}}
        <div class="card" style="margin-top: 1.5rem;" 
             x-data="{ 
                confirmDelete: false, 
                deleteFile: '', 
                selectedFiles: [], 
                confirmBatchDelete: false,
                allFiles: @js(array_column($backups, 'name')),
                toggleAll() {
                    if (this.selectedFiles.length === this.allFiles.length) {
                        this.selectedFiles = [];
                    } else {
                        this.selectedFiles = [...this.allFiles];
                    }
                }
             }">
            <div class="card-header" style="background: var(--bg-card); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h2 class="card-title"><i class="ph ph-database"></i> Backup History</h2>
                    <span class="badge badge-info">{{ count($backups) }} file{{ count($backups) !== 1 ? 's' : '' }}</span>
                </div>
                
                <div x-show="selectedFiles.length > 0" style="display: flex; align-items: center; gap: 12px; animation: modal-slide-up 0.2s ease-out;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--danger);">
                        <span x-text="selectedFiles.length"></span> files selected
                    </span>
                    <button type="button" class="btn-sm" style="background: var(--danger); color: white; border: none;" @click="confirmBatchDelete = true">
                        <i class="ph ph-trash"></i> Delete Selected
                    </button>
                </div>
            </div>
            <div class="card-body" style="padding: 0; overflow-x: auto;">
                <table class="settings-table">
                    <thead>
                        <tr>
                            <th style="width: 48px; text-align: center;">
                                <input type="checkbox" 
                                       :checked="selectedFiles.length === allFiles.length && allFiles.length > 0"
                                       @click="toggleAll()"
                                       style="width: 17px; height: 17px; cursor: pointer; accent-color: var(--accent-blue);">
                            </th>
                            <th>Backup File Name</th>
                            <th>Size</th>
                            <th>Date Created</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr :style="selectedFiles.includes('{{ $backup['name'] }}') ? 'background: rgba(239, 68, 68, 0.04)' : ''">
                            <td style="text-align: center;">
                                <input type="checkbox" value="{{ $backup['name'] }}" x-model="selectedFiles" 
                                       style="width: 17px; height: 17px; cursor: pointer; accent-color: var(--accent-blue);">
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; background: #fef3c7; color: #d97706; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="ph ph-file-zip"></i>
                                </div>
                                    <span style="font-weight: 600; color: var(--text-primary);">{{ $backup['name'] }}</span>
                                </div>
                            </td>
                            <td style="font-size: 0.8125rem; color: var(--text-secondary);">{{ $backup['size'] }}</td>
                            <td style="font-size: 0.8125rem; color: var(--text-secondary);">{{ $backup['date'] }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="{{ route('settings.maintenance.download', ['action' => 'download', 'filename' => $backup['name']]) }}"
                                       class="btn-sm" title="Download">
                                        <i class="ph ph-download-simple"></i>
                                    </a>
                                    
                                    <button type="button" class="btn-sm" style="color: var(--danger);" title="Delete"
                                            @click="confirmDelete = true; deleteFile = '{{ $backup['name'] }}'">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <i class="ph ph-database" style="font-size: 2.5rem; opacity: 0.3; display: block; margin-bottom: 8px;"></i>
                                No backups yet. Click "Create Backup Now" to get started.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Delete Confirm Modal --}}
            <template x-if="confirmDelete">
                <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
                    <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 420px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                        <div style="padding: 1.75rem 2rem;">
                            <div style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                                <i class="ph ph-trash" style="font-size: 1.5rem; color: var(--danger);"></i>
                            </div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Delete Backup?</h3>
                            <p style="font-size: 0.875rem; color: var(--text-secondary);">Are you sure you want to delete <strong x-text="deleteFile"></strong>? This action cannot be undone.</p>
                        </div>
                        <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                            <button @click="confirmDelete = false" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                                Cancel
                            </button>
                            <form action="{{ route('settings.maintenance') }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="filename" :value="deleteFile">
                                <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--danger); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="ph ph-trash"></i> Yes, Delete Permanently
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Batch Delete Confirm Modal --}}
            <template x-if="confirmBatchDelete">
                <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
                    <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 420px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                        <div style="padding: 1.75rem 2rem;">
                            <div style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                                <i class="ph ph-trash" style="font-size: 1.5rem; color: var(--danger);"></i>
                            </div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Delete Multiple Backups?</h3>
                            <p style="font-size: 0.875rem; color: var(--text-secondary);">You are about to delete <strong x-text="selectedFiles.length"></strong> files permanently. This action cannot be undone.</p>
                        </div>
                        <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                            <button @click="confirmBatchDelete = false" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                                Cancel
                            </button>
                            <form action="{{ route('settings.maintenance') }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="batch_delete">
                                <template x-for="file in selectedFiles" :key="file">
                                    <input type="hidden" name="filenames[]" :value="file">
                                </template>
                                <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--danger); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="ph ph-trash"></i> Yes, Delete All Selected
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- POST-BACKUP SUCCESS MODAL                           --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if(session('backup_success'))
    @push('modals')
    <div x-data="{ show: true }" x-show="show">
        <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.8); backdrop-filter: blur(8px); z-index: 999998;"></div>
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 999999; width: 100%; max-width: 440px; padding: 1rem;">
            <div class="card" style="width: 100%; text-align: center; padding: 2.5rem 2rem; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
            <div style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="ph ph-check-circle" style="font-size: 2.5rem;"></i>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">System Snapshot Complete</h2>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 2rem;">A secure record of your data has been saved to the server.</p>
            
            <div style="background: var(--gray-50); border-radius: 1rem; padding: 1.25rem; margin-bottom: 2rem; border: 1px dashed var(--gray-200);">
                <h3 style="font-size: 0.75rem; font-weight: 800; color: var(--warning); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="ph ph-warning-circle"></i> Vital Safety Step
                </h3>
                <p style="font-size: 0.8125rem; color: var(--text-primary); line-height: 1.5; font-weight: 600;">
                    Download this file now and save it to a <span style="color: var(--accent-blue);">USB Drive</span> or <span style="color: var(--success);">Google Drive</span>.
                </p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('settings.maintenance.download', ['action' => 'download', 'filename' => session('latest_backup')]) }}" 
                   class="btn-primary" 
                   style="height: 48px; display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--accent-blue); font-size: 0.875rem;">
                    <i class="ph ph-download-simple" style="font-size: 1.25rem;"></i> Download to This Computer
                </a>
                <button @click="show = false" 
                        class="btn-secondary" 
                        style="height: 44px; border: none; font-size: 0.8125rem;">
                    I'll do it later
                </button>
            </div>
        </div>
    </div>
        </div>
    </div>
    @endpush
    @endif

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB 4: Premium Branding (Hidden)                    --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if(\App\Models\Setting::get('is_branding_enabled'))
    <div class="settings-tab-panel" id="tab-branding" style="display:none;">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 24px; border: none;">
                <h2 class="card-title" style="color: white;"><i class="ph-duotone ph-paint-brush"></i> Barangay Logo Customization</h2>
                <p style="color: rgba(255,255,255,0.8); font-size: 0.8125rem; margin-top: 4px;">Customize the official branding of the Lupon Management System.</p>
            </div>
            <div class="card-body" style="padding: 32px;">
                <div style="display: grid; grid-template-columns: 240px 1fr; gap: 3rem; align-items: center;">
                    <div style="text-align: center;">
                        <p style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Current Visual Identity</p>
                        <div style="width: 180px; height: 180px; background: var(--gray-50); border-radius: 24px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px dashed var(--gray-200); margin: 0 auto; padding: 20px;">
                            @php $logoPath = $settings['barangay_logo_path'] ?? null; @endphp
                            @if($logoPath)
                                <img src="{{ asset($logoPath) }}" alt="Current Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            @else
                                <div style="text-align: center;">
                                    <i class="ph ph-image-square" style="font-size: 3.5rem; color: var(--gray-200);"></i>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">No logo uploaded</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <form action="{{ route('settings.update-branding') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="settings-field" style="margin-bottom: 24px;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 12px; text-transform: uppercase;">Official Logo File</label>
                                <div style="position: relative;">
                                    <input type="file" name="logo" id="logo-input" accept="image/*" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2;" required>
                                    <div style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 12px; transition: all 0.2s;">
                                        <div style="width: 40px; height: 40px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent-blue); border: 1px solid var(--gray-200);">
                                            <i class="ph ph-upload-simple"></i>
                                        </div>
                                        <span style="font-size: 0.8125rem; color: var(--text-secondary);">Click to choose a file...</span>
                                    </div>
                                </div>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 12px; line-height: 1.5;">
                                    <i class="ph ph-info" style="color: var(--accent-blue);"></i> Recommended: High-resolution PNG or SVG with a transparent background. Max file size: 2MB.
                                </p>
                            </div>
                            <div style="padding-top: 8px; border-top: 1px solid var(--gray-100);">
                                <button type="submit" class="btn-primary" style="background: var(--accent-blue); border-radius: 12px; padding: 12px 24px;">
                                    <i class="ph ph-check-circle"></i> Save Visual Identity
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- MEMBER MODAL (hidden by default)                       --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="settings-modal-overlay" id="memberModal" style="display: none;">
    <div class="card settings-modal-card">
        <div class="card-header">
            <h2 class="card-title" id="modalTitle">Add New Member</h2>
            <button class="btn-icon" onclick="closeMemberModal()"><i class="ph ph-x"></i></button>
        </div>
        <div class="card-body">
            <form id="memberForm" method="POST" action="{{ route('settings.members.store') }}">
                @csrf
                <input type="hidden" name="_method" id="memberFormMethod" value="POST">

                <div class="settings-field" style="margin-bottom: 16px;">
                    <label>Full Name</label>
                    <input type="text" name="name" id="memberName" required class="settings-input" placeholder="e.g. Juan De La Cruz">
                </div>

                <div class="settings-field" style="margin-bottom: 16px;">
                    <label>Position / Title</label>
                    <input type="text" name="position" id="memberPosition" required class="settings-input" placeholder="e.g. Lupon Member">
                </div>

                <div class="settings-field" style="margin-bottom: 16px;">
                    <label>Member Type</label>
                    <select name="member_type" id="memberType" class="settings-input">
                        <option value="regular">Regular Member</option>
                        <option value="lupon_secretary">Lupon Secretary</option>
                        <option value="punong_barangay">Punong Barangay</option>
                    </select>
                </div>

                <div class="settings-field" style="margin-bottom: 24px;">
                    <label>Appointment Date</label>
                    <input type="date" name="appointment_date" id="memberAppointment" class="settings-input">
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeMemberModal()">Cancel</button>
                    <button type="submit" class="btn-primary" id="memberSubmitBtn">Register Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ==========================================
   SETTINGS PAGE LAYOUT
   ========================================== */
.settings-container {
    padding: 28px 32px;
}

/* Tab Navigation */
.settings-tabs {
    display: flex;
    gap: 4px;
    border-bottom: 2px solid var(--gray-200);
    margin-bottom: 24px;
}

.settings-tab-btn {
    background: none;
    border: none;
    padding: 12px 20px;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    position: relative;
    transition: color 0.2s;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
}

.settings-tab-btn:hover {
    color: var(--accent-blue);
}

.settings-tab-btn.active {
    color: var(--accent-blue);
    font-weight: 700;
    border-bottom-color: var(--accent-blue);
}

/* Tab Panels */
.settings-tab-panel {
    animation: settingsFadeIn 0.25s ease-out;
}

@keyframes settingsFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Data Table */
.settings-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 700px;
}

.settings-table th {
    text-align: left;
    padding: 12px 24px;
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.settings-table td {
    padding: 14px 24px;
    font-size: 0.8125rem;
    border-bottom: 1px solid var(--gray-100);
    color: var(--text-secondary);
}

.settings-table tr:hover td {
    background: var(--gray-50);
}

/* Security Grid */
.settings-security-grid {
    display: block;
    max-width: 600px;
    gap: 24px;
}

.settings-form-section {
    margin-bottom: 0;
}

.settings-section-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
}

.settings-field {
    margin-bottom: 12px;
}

.settings-field label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-secondary);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.settings-input {
    width: 100%;
    padding: 9px 14px;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    font-size: 0.8125rem;
    color: var(--text-primary);
    background: var(--gray-50);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.settings-input:focus {
    outline: none;
    border-color: var(--accent-blue);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    background: var(--bg-card);
}

.settings-divider {
    height: 1px;
    background: var(--gray-200);
    margin: 28px 0;
}

.settings-password-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    max-width: 600px;
}

/* Recovery Code */
.settings-recovery-code-box {
    background: var(--warning-light);
    border: 2px dashed var(--warning);
    padding: 16px;
    border-radius: var(--radius-md);
    text-align: center;
    margin-bottom: 16px;
}

.settings-recovery-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--warning);
    text-transform: uppercase;
}

.settings-recovery-value {
    display: block;
    font-family: 'Courier New', monospace;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    padding: 8px 0;
    letter-spacing: 0.1em;
}

.settings-recovery-meta {
    margin-top: 16px;
    padding: 10px 12px;
    background: var(--gray-50);
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
    color: var(--text-secondary);
}

/* Backup Hero */
.settings-backup-hero {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border-radius: 1rem;
    padding: 28px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.settings-maintenance-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

/* Modal */
.settings-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.settings-modal-card {
    width: 100%;
    max-width: 480px;
    animation: settingsModalIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes settingsModalIn {
    from { opacity: 0; transform: scale(0.95) translateY(-16px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

/* ==========================================
   DARK MODE OVERRIDES FOR SETTINGS
   ========================================== */
[data-theme="dark"] .settings-input {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
}

[data-theme="dark"] .settings-input:focus {
    background: #0f172a;
    border-color: #60a5fa;
    color: #f1f5f9;
}

[data-theme="dark"] .settings-input::placeholder {
    color: #64748b;
}

[data-theme="dark"] .settings-input:disabled {
    background: #1e293b;
    color: #94a3b8;
    opacity: 0.6;
}

[data-theme="dark"] .settings-tab-btn {
    color: #94a3b8;
}

[data-theme="dark"] .settings-tab-btn:hover {
    color: #60a5fa;
}

[data-theme="dark"] .settings-tab-btn.active {
    color: #60a5fa;
    border-bottom-color: #60a5fa;
}

[data-theme="dark"] .settings-tabs {
    border-bottom-color: #334155;
}

[data-theme="dark"] .settings-table th {
    background: #1e293b;
    color: #64748b;
    border-bottom-color: #334155;
}

[data-theme="dark"] .settings-table td {
    border-bottom-color: #1e293b;
    color: #94a3b8;
}

[data-theme="dark"] .settings-table tr:hover td {
    background: #1e293b;
}

[data-theme="dark"] .settings-modal-card {
    background: #0f172a;
    border-color: #1e293b;
}

[data-theme="dark"] .settings-backup-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border: 1px solid #334155;
}

[data-theme="dark"] select.settings-input option {
    background: #0f172a;
    color: #f1f5f9;
}
</style>

@push('scripts')
<script>
    // Tab Switching
    function switchTab(tabName) {
        if (!tabName) return;
        
        document.querySelectorAll('.settings-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.settings-tab-panel').forEach(panel => {
            panel.style.display = 'none';
            panel.classList.remove('active');
        });

        const btn = document.querySelector('[data-tab="' + tabName + '"]');
        if (btn) btn.classList.add('active');
        
        const panel = document.getElementById('tab-' + tabName);
        if (panel) {
            panel.style.display = '';
            panel.classList.add('active');
        }
    }

    // Initialize tab from hash or default
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash.substring(1);
        if (hash) {
            switchTab(hash);
        }
    });

    // Member Modal
    function openMemberModal(member) {
        const modal = document.getElementById('memberModal');
        const form = document.getElementById('memberForm');
        const title = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('memberSubmitBtn');
        const methodField = document.getElementById('memberFormMethod');

        if (member) {
            title.textContent = 'Edit Lupon Member';
            submitBtn.textContent = 'Update Member';
            form.action = '/settings/members/' + member.id;
            methodField.value = 'PUT';
            document.getElementById('memberName').value = member.name;
            document.getElementById('memberPosition').value = member.position;
            document.getElementById('memberType').value = member.member_type;
            document.getElementById('memberAppointment').value = member.appointment_date || '';
        } else {
            title.textContent = 'Add New Member';
            submitBtn.textContent = 'Register Member';
            form.action = '{{ route("settings.members.store") }}';
            methodField.value = 'POST';
            document.getElementById('memberName').value = '';
            document.getElementById('memberPosition').value = '';
            document.getElementById('memberType').value = 'regular';
            document.getElementById('memberAppointment').value = '';
        }

        modal.style.display = 'flex';
    }

    function closeMemberModal() {
        document.getElementById('memberModal').style.display = 'none';
    }

    // Close modal on backdrop click
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('memberModal').addEventListener('click', function(e) {
            if (e.target === this) closeMemberModal();
        });
    });
</script>
@endpush
@endsection

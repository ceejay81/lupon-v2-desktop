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




    </nav>



    {{-- Tab Panels --}}

    @include('settings.partials.personnel')

    @include('settings.partials.security')

    @include('settings.partials.maintenance')





    {{-- Member Modal --}}

    @include('settings.partials.member-modal')



    {{-- Backup Success Modal --}}

    @include('settings.partials.backup-success-modal')



</div>



<style>

/* ==========================================

   SETTINGS PAGE STYLES

   ========================================== */

.settings-container {

    padding: 28px 32px;

}



.settings-tabs {

    display: flex;

    gap: 4px;

    border-bottom: 2px solid var(--gray-200);

    margin-bottom: 24px;

}



.settings-tab-btn {

    padding: 12px 20px;

    border: none;

    background: none;

    color: var(--text-secondary);

    font-size: 0.875rem;

    font-weight: 600;

    cursor: pointer;

    border-bottom: 2px solid transparent;

    transition: all 0.2s;

}



.settings-tab-btn:hover {

    color: var(--accent-blue);

}



.settings-tab-btn.active {

    color: var(--accent-blue);

    border-bottom-color: var(--accent-blue);

}



.settings-tab-panel {

    animation: fadeIn 0.3s ease-out;

}



.settings-tab-panel[style*="display:none"] {

    display: none !important;

}



.settings-input {

    width: 100%;

    padding: 10px 14px;

    border: 1px solid var(--gray-200);

    border-radius: var(--radius-md);

    font-size: 0.875rem;

    transition: all 0.2s;

}



.settings-input:focus {

    outline: none;

    border-color: var(--accent-blue);

    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);

}



.settings-table {

    width: 100%;

    border-collapse: collapse;

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


    color: var(--text-secondary);

}



@keyframes fadeIn {

    from { opacity: 0; transform: translateY(8px); }

    to { opacity: 1; transform: translateY(0); }

}



@keyframes spin {

    to { transform: rotate(360deg); }

}



.settings-modal-overlay {

    position: fixed;

    inset: 0;

    background: rgba(15, 23, 42, 0.6);

    backdrop-filter: blur(8px);

    z-index: 9999;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 20px;

}



.settings-modal-card {

    width: 100%;

    max-width: 500px;

    background: var(--bg-card);

    border-radius: var(--radius-lg, 16px);

    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);

    animation: modalSlideUp 0.3s ease-out;

    overflow: hidden;

}



@keyframes modalSlideUp {

    from { opacity: 0; transform: translateY(20px); }

    to { opacity: 1; transform: translateY(0); }

}



.full-screen-loading {

    position: fixed !important;

    inset: 0 !important;

    width: 100vw !important;

    height: 100vh !important;

    background: rgba(15, 23, 42, 0.98) !important;

    z-index: 2147483647 !important;

    display: grid;

    place-items: center !important;

    align-content: center !important;

    text-align: center !important;

    padding: 2rem !important;

}



.loading-spinner {

    width: 72px;

    height: 72px;

    border: 5px solid rgba(255, 165, 0, 0.1);

    border-top-color: #ffa500;

    border-radius: 50%;

    animation: spin 1s linear infinite;

    margin: 0 auto 2rem auto;

}



.loading-text-container {

    max-width: 600px;

    margin: 0 auto;

}



.loading-text-container h2 {

    color: white;

    font-size: 2rem;

    font-weight: 800;

    margin: 0 0 1rem 0;

    letter-spacing: -0.02em;

}



.loading-text-container p {

    color: rgba(255, 255, 255, 0.6);

    font-size: 1.125rem;

    line-height: 1.6;

    margin: 0;

}



.loading-text-container strong {

    color: #ffa500;

}

</style>



@push('scripts')

<script>

    function switchTab(tabName) {

        document.querySelectorAll('.settings-tab-panel').forEach(panel => {

            panel.style.display = 'none';

        });

        

        document.querySelectorAll('.settings-tab-btn').forEach(btn => {

            btn.classList.remove('active');

        });

        

        const selectedPanel = document.getElementById('tab-' + tabName);

        if (selectedPanel) {

            selectedPanel.style.display = 'block';

        }

        

        event.target.closest('.settings-tab-btn').classList.add('active');

    }

</script>

@endpush

@endsection

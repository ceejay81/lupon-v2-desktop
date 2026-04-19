@php
    $isArchive = $isArchive ?? false;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('doc-title', 'Barangay Document') — {{ isset($case) ? $case->case_number : 'Report' }}</title>

    <!-- Shared Editor Styles -->
    <link rel="stylesheet" href="{{ asset('documents/css/editor-style.css') }}">

    @unless($isArchive)
        <!-- Shared Editor Toolbar & Logic -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            window.SAVE_ROUTE = "{{ isset($case) ? route('cases.export.save-content', $case->id) : route('reports.save-content') }}";
            window.CASE_ID = "{{ isset($case) ? $case->id : '' }}";
            window.REPORT_MONTH = "{{ $month ?? '' }}";
            window.REPORT_YEAR = "{{ $year ?? '' }}";
            window.DOCUMENT_TYPE = "@yield('doc-type', 'Document')";
            window.BRANDING_UNLOCKED = {{ \App\Models\Setting::get('is_branding_enabled', false) ? 'true' : 'false' }};

            window.unlockBranding = (code) => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("settings.unlock-branding") }}';
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const codeInput = document.createElement('input');
                codeInput.type = 'hidden';
                codeInput.name = 'code';
                codeInput.value = code;
                form.appendChild(csrfInput);
                form.appendChild(codeInput);
                document.body.appendChild(form);
                form.submit();
            }

            window.lockBranding = () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("settings.lock-branding") }}';
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        </script>
        <script src="{{ asset('documents/js/editor-toolbar.js') }}" defer></script>
    @endunless

    <style>
        /* Custom tweaks for Laravel integration */
        #doc-body {
            /* Ensure the content is properly spaced for the double-click tool */
            min-height: 10.5in;
        }

        @if($isArchive)
            /* Archive-mode specific stability tweaks */
            body { background-color: #525659 !important; }
            .paper-wrapper { margin: 0 auto; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6); }

            /* Professional Print Fidelity */
            @media print {
                body { background-color: white !important; }
                .paper-wrapper { box-shadow: none !important; width: 100% !important; margin: 0 !important; }
            }
        @endif

        @media print {
            .no-print { display: none !important; }
            
            /* The Ghost Layer: Invisible on screen, fixed position on every page in print */
            .print-ghost-header {
                display: block !important;
                position: fixed !important;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 9999;
                pointer-events: none;
            }

            .print-ghost-watermark {
                position: fixed !important;
                top: 25%;
                left: 0;
                right: 0;
                width: 8in;
                margin: 0 auto;
                opacity: 0.1 !important;
                z-index: -1;
                pointer-events: none;
                display: block !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Hide the original non-fixed items during print to avoid doubling on Page 1 */
            .paper-wrapper .header, .paper-wrapper .watermark {
                visibility: hidden !important;
                height: 0;
                margin: 0;
            }

            /* Allow page-wrapper to expand across multiple sheets */
            .paper-wrapper {
                box-shadow: none !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                overflow: visible !important;
                display: block !important;
            }

            #doc-body {
                margin-top: 1.8in !important; /* Space for fixed header on Page 2, 3, etc. */
            }
        }

        /* Screen behavior: perfectly untouched */
        .print-ghost-header, .print-ghost-watermark {
            display: none;
        }
    </style>

    @if(request('print'))
        <script>
            window.onload = () => {
                setTimeout(() => {
                    window.print();
                }, 500);
            };
        </script>
    @endif
    @stack('styles')
</head>

<body class="antialiased">
    <!-- PRINT GHOST LAYER -->
    <div class="print-ghost-header">
        <img src="{{ $isArchive && isset($header) ? $header : asset('documents/images/header.png') }}" 
             style="width: 100%;" alt="Official Header" />
    </div>
    <img src="{{ $isArchive && isset($watermark) ? $watermark : asset('documents/images/watermark.png') }}" 
         class="print-ghost-watermark" alt="Official Watermark" />

    @unless(request('print'))
    @php
        $backUrl = route('dashboard');
        $backLabel = 'Back';

        if (isset($case)) {
            $backUrl = route('cases.show', $case);
            $backLabel = 'Back to Case ' . $case->case_number;
        } elseif (isset($month) && isset($year)) {
            $backUrl = route('reports.index', ['month' => $month, 'year' => $year]);
            $backLabel = 'Back to Reports';
        } elseif (isset($case_id) && $case_id) {
            $backUrl = route('cases.show', $case_id);
            $backLabel = 'Back to Case';
        }
    @endphp
    <div class="no-print" style="position: fixed; top: 1rem; left: 1rem; z-index: 9999; display: flex; gap: 0.5rem;">
        <a href="{{ $backUrl }}"
            style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.25rem; background: white; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8rem; font-weight: 700; color: #1e293b; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.08); font-family: inherit; text-decoration: none; transition: all 0.2s;"
            onmouseover="this.style.background='#f8fafc'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(0,0,0,0.12)';" 
            onmouseout="this.style.background='white'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256">
                <path d="M224,128a8,8,0,0,1-8,8H59.31l58.35,58.34a8,8,0,0,1-11.32,11.32l-72-72a8,8,0,0,1,0-11.32l72-72a8,8,0,0,1,11.32,11.32L59.31,120H216A8,8,0,0,1,224,128Z"/>
            </svg>
            {{ $backLabel }}
        </a>
    </div>
    @endunless

    <div class="paper-wrapper">
        <div class="header">
            <!-- Normal Header: Visible on screen, hidden in print because the Ghost Header takes over -->
            <img id="header-img"
                src="{{ $isArchive && isset($header) ? $header : asset('documents/images/header.png') }}"
                class="header-banner" style="width: 100%;" alt="" />
        </div>

        <!-- WATERMARK -->
        <div class="watermark">
            <img id="watermark-img"
                src="{{ $isArchive && isset($watermark) ? $watermark : asset('documents/images/watermark.png') }}"
                class="watermark-bg" alt="" />
        </div>

        <div id="doc-body" contenteditable="{{ $isArchive ? 'false' : 'true' }}" spellcheck="true">
            @if(isset($savedContent) && $savedContent)
                {!! $savedContent !!}
            @else
                @yield('content')
            @endif
        </div>
    </div><!-- /paper-wrapper -->

</body>

</html>
@extends('documents.layout')

@section('doc-title', 'ENDORSEMENT LETTER')
@section('doc-type', 'Endorsement Letter')

@push('styles')
<style>
    /* Endorsement Specific Styles */
    .meta-section { margin: 30px 0; }
    .meta-row { display: flex; margin-bottom: 5px; }
    .meta-label { width: 100px; font-weight: bold; }
    .meta-value { flex: 1; }
    .endorsement-header {
        color: #000099;
        font-size: 15pt;
        border-bottom: 3px double #000;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }
    .sender-block {
        margin-top: 50px;
        width: 300px;
    }
    .cc-block {
        margin-top: 40px;
        font-size: 10pt;
        font-style: italic;
    }
    .footer-badge {
        margin-top: 50px;
        text-align: center;
    }
    .font-bold { font-weight: bold; }
    .italic { font-style: italic; }
</style>
@endpush

@section('content')
<!-- Date Section -->
<div class="meta-section">
    <div class="meta-row">
        <span class="meta-label">DATE</span>
        <span class="meta-value">: <span class="doc-field doc-field-medium">{{ strtoupper(date('F j, Y')) }}</span></span>
    </div>
</div>

<!-- Recipient Section -->
<div class="meta-section">
    <div class="meta-row">
        <span class="meta-label">TO</span>
        <span class="meta-value">: <span class="font-bold">MS. MARIA THERESA BAUTISTA</span></span>
    </div>
    <div style="margin-left: 105px;">
        <span class="italic font-bold">City Director DILG</span><br>
        <span class="italic">General Santos City</span>
    </div>
</div>

<!-- Subject Section -->
<div class="meta-section">
    <div class="meta-row">
        <span class="meta-label">SUBJECT</span>
        <span class="meta-value">: <span class="endorsement-header">ENDORSEMENT LETTER</span></span>
    </div>
</div>

<!-- Salutation -->
<p class="italic font-bold" style="margin: 30px 0;">Magandang Gensan!</p>

<!-- Body Content -->
<p style="text-indent: 0.5in; text-align: justify;">
    Respectfully forwarded to <span class="font-bold">MA. THERESA BAUTISTA</span>, City Director, Department of the Interior and Local Government (DILG), City of General Santos the herein Report of Cases filed and action taken and problems encountered on KP Implementation for the period from <span class="font-bold">{{ strtoupper(date('F 1, Y')) }}</span> of this LGU as required pursuant to RA 7160 hereto attached for your ready reference, perusal, documentation, appropriate legislative action and disposition.
</p>

<p style="text-indent: 0.5in; margin-top: 20px;">
    Thank you and God bless! 
</p>

<p style="margin-top: 40px;">In Barangay {{ $settings['barangay_name'] ?? 'Bula' }},</p>

<!-- Signature Block -->
<div class="sender-block">
    <p class="font-bold" style="margin-bottom: 0;">{{ strtoupper($settings['punong_barangay'] ?? $settings['barangay_captain'] ?? 'HON. NICANORA T. VARGAS') }}</p>
    <p style="margin: 0;">Punong Barangay</p>
    <p style="margin: 0;">LGU-{{ $settings['barangay_name'] ?? 'Bula' }}</p>
</div>

<!-- CC Section -->
<div class="cc-block">
    <p>Cc:</p>
    <ul style="list-style: disc; margin-left: 20px;">
        <li>Hon. {{ $settings['committee_peace_order'] ?? 'Dante Granada' }} – Committee on peace and Order, Public Welfare and Safety – Brgy. {{ $settings['barangay_name'] ?? 'Bula' }}</li>
        <li>Mr. {{ $settings['lupon_president'] ?? 'JIMUEL VILLOTE' }} – Lupon Tagapamayapa President</li>
        <li>Received Copy File</li>
    </ul>
</div>
@endsection

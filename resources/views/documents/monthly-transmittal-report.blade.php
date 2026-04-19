@extends('documents.layout')

@section('doc-title', 'MONTHLY TRANSMITTAL REPORT')
@section('doc-type', 'monthly-transmittal')

@push('styles')
<style>
    /* Report Specific Styles */
    .report-title {
        font-size: 14pt;
        font-weight: bold;
        text-align: center;
        margin: 20px 0 10px 0;
        text-decoration: underline;
    }

    .report-period {
        text-align: center;
        margin-bottom: 30px;
        font-weight: bold;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 40px;
    }

    .data-table th, .data-table td {
        border: 1px solid #000;
        padding: 5px;
        text-align: left;
        vertical-align: top;
    }

    .data-table th {
        background-color: #f2f2f2;
        text-align: center;
        font-weight: bold;
    }

    .signature-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        margin-top: 50px;
    }

    .signature-block {
        width: 100%;
    }

    .footer-badge {
        margin-top: 50px;
        text-align: center;
    }
    .font-bold { font-weight: bold; }
    .underline { text-decoration: underline; }
    .text-center { text-align: center; }
</style>
@endpush

@section('content')
<div class="report-title">MONTHLY TRANSMITTAL REPORT</div>
<div class="report-period">
    <span class="doc-field doc-field-medium">{{ strtoupper($period->format('F 1')) }}</span> TO 
    <span class="doc-field doc-field-medium">{{ strtoupper($period->format('F t, Y')) }}</span>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 15%;">CASE NO.</th>
            <th style="width: 25%;">COMPLAINANTS</th>
            <th style="width: 25%;">RESPONDENTS</th>
            <th style="width: 35%;">STATUS OF CASES</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cases ?? [] as $case_item)
        @php /** @var \App\Models\LuponCase $case_item */ @endphp
        <tr>
            <td class="text-center">{{ $case_item->case_number }}</td>
            <td>{{ strtoupper($case_item->complainants->pluck('name')->join(', ') ?: $case_item->complainant) }}</td>
            <td>{{ strtoupper($case_item->respondents->pluck('name')->join(', ') ?: $case_item->respondent) }}</td>
            <td>{{ strtoupper($case_item->status) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">No cases recorded for this period.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<p class="font-bold">Summary of Cases:</p>
<ul style="list-style: none; padding-left: 0;">
    <li>Settled: {{ ($cases ?? collect())->where('status', 'settled')->count() }}</li>
    <li>Repudiated: {{ ($cases ?? collect())->where('status', 'repudiated')->count() }}</li>
    <li>On-Process: {{ ($cases ?? collect())->whereIn('status', ['under_mediation', 'under_conciliation', 'under_arbitration'])->count() }}</li>
    <li><span class="font-bold">Total Cases: {{ ($cases ?? collect())->count() }}</span></li>
</ul>

<div class="signature-grid">
    <div class="signature-block">
        <p style="margin-bottom: 40px;">Prepared by:</p>
        <p style="margin-bottom: 0;"><span class="doc-field doc-field-medium">{{ strtoupper($settings['lupon_president'] ?? 'JIMUEL VILLOTE') }}</span></p>
        <p style="margin: 0;">Lupon President</p>
    </div>
    <div class="signature-block">
        <p style="margin-bottom: 40px;">Noted by:</p>
        <p style="margin-bottom: 0;"><span class="doc-field doc-field-medium">{{ strtoupper($settings['punong_barangay'] ?? $settings['barangay_captain'] ?? 'HON. NICANORA T. VARGAS') }}</span></p>
        <p style="margin: 0;">Punong Barangay</p>
    </div>
</div>

<div style="clear: both; padding-bottom: 80px;"></div>
@endsection

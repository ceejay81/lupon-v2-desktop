@extends('documents.layout')

@section('doc-title', 'NOTICE OF HEARING')
@section('doc-type', 'Notice of Hearing (KP Form 8)')

@section('content')
<div style="font-family: Arial, sans-serif;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4in;">
        <div style="width: 50%;">
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span class="doc-field doc-field-medium" data-field="complainant">{{ strtoupper($case->complainants->pluck('name')->join(', ') ?: $case->complainant ?? '') }}</span></p>
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span class="doc-field doc-field-long" data-field="complainant_address">{{ $case->complainants->first()->address ?? $case->complainant_address ?? '' }}</span></p>
            <p style="margin: 0; line-height: 1.15; font-size: 11pt;">{{ $settings['city_name'] ?? 'General Santos City' }}</p>
            <p style="margin: 0; line-height: 1.15; font-size: 10pt;">===================</p>
            <p style="margin: 0; font-style: italic; font-size: 9pt; text-indent: 0.2in;">Complainant/s</p>
        </div>
        <div style="width: 35%;">
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 10pt;">Brgy. Case #: <span class="doc-field-short doc-field">{{ $case->case_number ?? '' }}</span></p>
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 10pt; margin-top: 0.1in;">For: <span class="doc-field doc-field-medium">{{ $case->nature_of_complaint ?? $case->case_type ?? '' }}</span></p>
        </div>
    </div>
    
    <p style="margin: 0.1in 0; text-indent: 0.5in; font-size: 10pt;">~against~</p>

    <div style="margin-bottom: 0.5in;">
        <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span class="doc-field doc-field-medium" data-field="respondent">{{ strtoupper($case->respondents->pluck('name')->join(', ') ?: $case->respondent ?? '') }}</span></p>
        <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span class="doc-field doc-field-long" data-field="respondent_address">{{ $case->respondents->first()->address ?? $case->respondent_address ?? '' }}</span></p>
        <p style="margin: 0; line-height: 1.15; font-size: 11pt;">{{ $settings['city_name'] ?? 'General Santos City' }}</p>
        <p style="margin: 0; line-height: 1.15; font-size: 10pt;">===================</p>
        <p style="margin: 0; font-style: italic; font-size: 9pt; text-indent: 0.2in;">Respondent/s</p>
    </div>

    <h2 style="text-align: center; font-size: 18pt; font-weight: bold; margin-bottom: 0.4in;">NOTICE OF HEARING</h2>

    <p style="margin-bottom: 0.2in;"><b>TO:	<span class="doc-field doc-field-long">{{ strtoupper($case->complainants->pluck('name')->join(', ') ?: $case->complainant ?? '') }}</span></b></p>
    <p style="margin-bottom: 0.2in; text-indent: 0.5in;"><span class="doc-field doc-field-long"></span></p>
    <p style="margin-bottom: 0.1in; text-indent: 0.5in;"><span class="doc-field doc-field-long"></span></p>
    <p style="margin-left: 0.5in; font-style: italic; margin-bottom: 0.5in;">Complainant/s</p>

    <p style="text-indent: 0.5in; text-align: justify; line-height: 2.0; font-size: 11pt; margin-bottom: 0.4in;">You are hereby required to appear before me on the <span class="doc-field-short doc-field"></span> day of <span class="doc-field-medium doc-field"></span>, 20<span class="doc-field-short doc-field"></span> at <span class="doc-field-short doc-field"></span> o’clock in the <span class="doc-field-medium doc-field"></span> for the hearing of your complaint.</p>

    <p style="text-indent: 0.5in; font-size: 11pt; margin-bottom: 0.6in;">This <span class="doc-field-short doc-field"></span> day of <span class="doc-field-medium doc-field"></span>, 20<span class="doc-field-short doc-field"></span>.</p>

    <div style="width: 50%; margin-left: auto; text-align: center;">
        <p style="margin: 0; font-weight: bold; font-size: 11pt;">{{ strtoupper($settings['punong_barangay'] ?? $settings['barangay_captain'] ?? 'HON. NICANORA T. VARGAS') }}</p>
        <p style="margin: 0; font-size: 11pt;">Punong Barangay</p>
        <p style="margin: 0; font-size: 11pt;">Lupon Chairman</p>
    </div>

    <div style="margin-top: 0.8in;">
        <p style="text-indent: 0.5in; line-height: 2.0; margin-bottom: 0.4in;">Notified this <span class="doc-field-short doc-field"></span> day of <span class="doc-field-medium doc-field"></span>, 20<span class="doc-field-short doc-field"></span>.</p>
        
        <div style="width: 50%; margin-left: auto;">
            <p style="margin-bottom: 0.1in;">Received by:</p>
            <p style="font-style: italic; font-size: 10pt; margin-bottom: 0.05in; margin-left: 0.3in;">Complainant/s:</p>
            <p style="margin-bottom: 0.4in;"><span class="doc-field doc-field-long"></span></p>
            <p style="margin-bottom: 0.05in;"><span class="doc-field doc-field-long">{{ strtoupper($case->complainants->pluck('name')->join(', ') ?: $case->complainant ?? '') }}</span></p>
            <p style="font-style: italic; font-size: 9pt; margin-left: 0.3in;">Name and Signature</p>
        </div>
    </div>
</div>
@endsection

@extends('documents.layout')

@section('doc-title', 'STATUS OF CASE')

@section('content')
<div style="font-family: Arial, sans-serif;">
    <h2 style="text-align: center; font-size: 20pt; font-weight: bold; margin-bottom: 0.5in; text-decoration: underline;">STATUS OF CASE</h2>

    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4in;">
        <div style="width: 50%;">
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span class="doc-field doc-field-medium" data-field="complainant">{{ strtoupper($case->complainantCitizen->full_name ?? $case->complainant ?? '') }}</span></p>
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span class="doc-field doc-field-long" data-field="complainant_address">{{ $case->complainantCitizen->address ?? '' }}</span></p>
            <p style="margin: 0; line-height: 1.15; font-size: 11pt;">{{ $settings['city_name'] ?? 'General Santos City' }}</p>
            <p style="margin: 0; line-height: 1.15; font-size: 10pt;">===================</p>
            <p style="margin: 0; font-style: italic; font-size: 9pt; text-indent: 0.2in;">Complainant/s</p>
        </div>
        <div style="width: 35%;">
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 10pt;">Brgy. Case #: <span class="doc-field-short doc-field" data-field="case_number">{{ $case->case_number ?? '' }}</span></p>
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 10pt; margin-top: 0.1in;">For: <span class="doc-field doc-field-medium" data-field="nature_of_case">{{ $case->nature_of_complaint ?? $case->case_type ?? '' }}</span></p>
        </div>
    </div>
    
    <p style="margin: 0.1in 0; text-indent: 0.5in; font-size: 10pt;">~against~</p>

    <div style="margin-bottom: 0.5in;">
        <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span class="doc-field doc-field-medium" data-field="respondent">{{ strtoupper($case->respondentCitizen->full_name ?? $case->respondent ?? '') }}</span></p>
        <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span class="doc-field doc-field-long" data-field="respondent_address">{{ $case->respondentCitizen->address ?? '' }}</span></p>
        <p style="margin: 0; line-height: 1.15; font-size: 11pt;">{{ $settings['city_name'] ?? 'General Santos City' }}</p>
        <p style="margin: 0; line-height: 1.15; font-size: 10pt;">===================</p>
        <p style="margin: 0; font-style: italic; font-size: 9pt; text-indent: 0.2in;">Respondent/s</p>
    </div>

    <div style="margin: 0.5in 0; border: 1px solid #000; padding: 20px;">
        <p style="margin: 0; font-size: 14pt;"><b>Remarks:</b> <span class="doc-field doc-field-long">{{ strtoupper($case->status ?? 'OPEN') }}</span></p>
        <p style="margin: 20px 0 0 0; font-size: 14pt;"><b>Date:</b> <span class="doc-field doc-field-medium">{{ date('F j, Y') }}</span></p>
    </div>

    <div style="margin-top: 1in; display: flex; justify-content: space-between;">
        <div style="width: 45%; text-align: center;">
            <p style="margin: 0; font-weight: bold; font-size: 11pt;"><span class="doc-field doc-field-medium">{{ strtoupper($settings['barangay_secretary'] ?? '') }}</span></p>
            <p style="margin: 0; font-size: 11pt;">Barangay Secretary</p>
        </div>
        <div style="width: 45%; text-align: center;">
            <p style="margin: 0; font-size: 11pt; font-style: italic; margin-bottom: 0.3in;">Noted by:</p>
            <p style="margin: 0; font-weight: bold; font-size: 11pt;">{{ strtoupper($settings['punong_barangay'] ?? $settings['barangay_captain'] ?? 'HON. NICANORA T. VARGAS') }}</p>
            <p style="margin: 0; font-size: 11pt;">Punong Barangay/Lupon Chairman</p>
        </div>
    </div>
</div>
@endsection

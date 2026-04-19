@extends('documents.layout')

@section('doc-title', 'CERTIFICATION TO FILE ACTION')
@section('doc-type', 'Certification to File Action (KP Form 20)')

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
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 10pt;">Brgy. Case #: <span class="doc-field-short doc-field" data-field="case_number">{{ ($case->case_number ?? '') ?? '' }}</span></p>
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 10pt; margin-top: 0.1in;">For: <span class="doc-field doc-field-medium" data-field="nature_of_case">{{ (($case->nature_of_complaint ?? $case->case_type ?? '') ?? '') }}</span></p>
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

    <h2 style="text-align: center; font-size: 18pt; font-weight: bold; margin-bottom: 0.4in;">CERTIFICATION TO FILE ACTION</h2>

    <p style="margin-bottom: 0.3in; font-style: italic; font-size: 11pt;">This is to certify that:</p>

    <div style="margin-left: 0.5in; margin-bottom: 0.4in; font-size: 11pt;">
        <ol style="font-weight: bold; line-height: 1.5;">
            <li style="margin-bottom: 0.15in; text-align: justify;">There has been personal confrontation between the parties before the Punong Barangay/Pangkat ng Tagapagkasundo;</li>
            <li style="margin-bottom: 0.15in; text-align: justify;">The Pangkat ng Tagapagkasundo was constituted but the personal confrontation before the Pangkat likewise did not result into a settlement; and</li>
            <li style="margin-bottom: 0.15in; text-align: justify;">Therefore, the corresponding complaint for the dispute may now be filed in court/government office.</li>
        </ol>
    </div>

    <p style="text-indent: 0.5in; font-size: 11pt; margin-bottom: 0.8in;">This <span class="doc-field doc-field-short">{{ date('jS') }}</span> day of <span class="doc-field doc-field-medium">{{ date('F Y') }}</span>.</p>

    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4in;">
        <div style="width: 45%; text-align: center;">
            <p style="margin: 0; font-weight: bold; font-size: 11pt;"><span class="doc-field doc-field-medium">{{ strtoupper($case->pangkats->first()->secretary->full_name ?? ($case->pangkat_secretary ?? '')) }}</span></p>
            <p style="margin: 0; font-size: 11pt;">Pangkat Secretary</p>
        </div>
        <div style="width: 45%; text-align: center;">
            <p style="margin: 0; font-weight: bold; font-size: 11pt;"><span class="doc-field doc-field-medium">{{ strtoupper($case->pangkats->first()->chairperson->full_name ?? ($case->pangkat_chairman ?? '')) }}</span></p>
            <p style="margin: 0; font-size: 11pt;">Pangkat Chairman</p>
        </div>
    </div>

    <div style="margin-top: 0.6in;">
        <p style="margin-bottom: 0in; font-weight: bold; font-size: 11pt;">Attested by:</p>
        <div style="width: 50%; margin-left: auto; text-align: center;">
            <p style="margin: 0; font-weight: bold; font-size: 11pt;">{{ strtoupper($settings['punong_barangay'] ?? $settings['barangay_captain'] ?? 'HON. NICANORA T. VARGAS') }}</p>
            <p style="margin: 0; font-size: 11pt;">Punong Barangay/Lupon Chairman</p>
        </div>
    </div>
</div>
@endsection

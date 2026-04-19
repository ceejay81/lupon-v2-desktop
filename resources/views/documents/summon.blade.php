@extends('documents.layout')

@section('doc-title', 'SUMMONS')
@section('doc-type', 'Summons (KP Form 9)')

@section('content')
<!-- Top Info Grid -->
<div style="display: flex; justify-content: space-between; font-family: Arial, sans-serif;">
    <div style="width: 45%; font-size: 10pt;">
        <p style="margin: 0; line-height: 1.4;">KP FORM NO. 09</p>
        <br>
        <p style="margin: 0; line-height: 1.4;">{{ $settings['city_name'] ?? 'General Santos City' }}</p>
        <p style="margin: 0; line-height: 1.4; overflow: hidden; white-space: nowrap;">========================</p>
        <p style="margin: 0; font-weight: bold; line-height: 1.4;"><span class="doc-field doc-field-medium" data-field="complainant">{{ strtoupper($case->complainants->pluck('name')->join(', ') ?: $case->complainant ?? '') }}</span></p>
        <p style="margin: 0; font-weight: bold; line-height: 1.4; text-indent: 0.28in;">-Against-</p>
        <br>
        <p style="margin: 0; font-weight: bold; line-height: 1.4; border-bottom: 1px solid black;">&nbsp;</p>
        <br>
        <p style="margin: 0; line-height: 1.4;">{{ $settings['city_name'] ?? 'General Santos City' }}</p>
        <p style="margin: 0; line-height: 1.4; overflow: hidden; white-space: nowrap;">========================</p>
        <p style="margin: 0; font-weight: bold; line-height: 1.4;"><span class="doc-field doc-field-medium" data-field="respondent">{{ strtoupper($case->respondents->pluck('name')->join(', ') ?: $case->respondent ?? '') }}</span></p>
        <p style="margin: 0; font-weight: bold; line-height: 1.4;">Respondent/S</p>
    </div>

    <div style="width: 50%; font-size: 10pt;">
        <!-- Container for Case Info (Header Right) -->
        <div style="display: flex; flex-direction: column; align-items: flex-end; width: 100%;">
            <div style="display: flex; align-items: baseline; justify-content: flex-end; width: 100%;">
                <span style="font-weight: bold; margin-right: 8px; min-width: 1.1in; text-align: right;">Brgy. Case #:</span>
                <span class="doc-field doc-field-medium" data-field="case_number">{{ $case->case_number ?? '' }}</span>
            </div>
            <div style="display: flex; align-items: baseline; justify-content: flex-end; width: 100%; margin-top: 8px;">
                <span style="font-weight: bold; margin-right: 8px; min-width: 1.1in; text-align: right;">For:</span>
                <span class="doc-field doc-field-long" data-field="nature_of_case">{{ $case->nature_of_complaint ?? $case->case_type ?? '' }}</span>
            </div>
        </div>

        <div style="text-align: center; margin: 15px 0;">
            <h1 style="font-family: 'Arial Black', sans-serif; font-size: 18pt; letter-spacing: 5px; margin-bottom: 5px; font-weight: bold;">S U M M O N S</h1>
        </div>

        <!-- Container for Respondent Info (TO: block) -->
        <div style="display: flex; align-items: flex-start; margin-top: 10px; width: 100%;">
            <span style="font-weight: bold; width: 40px; margin-top: 2px;">TO:</span>
            <div style="display: flex; flex-direction: column; gap: 5px; flex: 1;">
               <div class="field-item"><span class="doc-field doc-field-medium" data-field="respondent">{{ $case->respondents->pluck('name')->join(', ') ?: $case->respondent ?: '____________________' }}</span></div>
               <div class="field-label">Respondent/s</div>
               <span class="doc-field doc-field-long" data-field="respondent_address">{{ $case->respondentStatus->address ?? $case->respondents->first()->address ?? $case->respondent_address ?? '' }}</span>
                <span class="doc-field doc-field-long">{{ $settings['city_name'] ?? 'General Santos City' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Message -->
<div style="margin-top: 15px; font-size: 10pt; text-indent: 0.5in; font-family: Arial, sans-serif;">
    <p style="margin-bottom: 10px; line-height: 1.8; text-align: justify;">You are hereby summoned to appear before me in person, together <b>with your witnesses, on the</b>
       <span class="doc-field doc-field-short"></span> <b>day of</b>
       <span class="doc-field doc-field-medium"></span>
       <b>202</b><span class="doc-field" style="min-width: 25px;">{{ date('y') }}</span>
       <b>at</b> <span class="doc-field doc-field-short"></span>
       <b>o'clock in the</b> <span class="doc-field doc-field-medium"></span>, then and there to answer to a complaint made before me, copy of which is attached hereto, for <b>mediation/conciliation</b> of your dispute with complainant/s.</p>

    <p style="margin-bottom: 10px; line-height: 1.4; text-align: justify;">You are hereby warned that you refused or willfully fail to appear in obedience to this summon, you may barred from filing any counterclaim arising from said complaint.</p>

    <p style="margin-bottom: 10px; line-height: 1.4; text-indent: 0;">FAIL, NOT or else face punishment as for contempt of court.</p>

    <p style="margin-bottom: 10px; line-height: 1.8; text-indent: 0;"><b>This</b>
       <span class="doc-field doc-field-short">{{ date('d') }}</span>
       <b>day of</b>
       <span class="doc-field doc-field-medium">{{ date('F') }}</span>,
       <b>20</b><span class="doc-field" style="min-width: 25px;">{{ date('y') }}</span>.</p>
</div>

<div style="float: right; text-align: center; margin-top: 10px; width: 3in; font-family: Arial, sans-serif;">
    <p style="margin: 0; font-weight: bold; font-size: 11pt;">{{ strtoupper($settings['punong_barangay'] ?? $settings['barangay_captain'] ?? 'HON. NICANORA T. VARGAS') }}</p>
    <p style="margin: 0; font-weight: bold;">Punong Barangay</p>
    <p style="margin: 0; font-weight: bold;">Lupon Chairman</p>
</div>
<div style="clear: both;"></div>

<hr style="border: none; border-top: 1px solid black; margin: 15px 0 10px 0;" />

<!-- Officer's Return -->
<div style="font-size: 10pt; font-family: Arial, sans-serif;">
    <p style="text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 10px;">OFFICER'S RETURN</p>

    <p style="margin-bottom: 10px; line-height: 1.8; text-indent: 0;">I served this summon upon
       <span class="doc-field doc-field-long">{{ strtoupper($case->respondents->pluck('name')->join(', ') ?: $case->respondent ?? '') }}</span>
       on the <span class="doc-field doc-field-short">{{ date('d') }}</span>
       day of <span class="doc-field doc-field-medium">{{ date('F') }}</span>,
       <b>20</b><span class="doc-field" style="min-width: 25px;">{{ date('y') }}</span> by:</p>

    <div style="margin-left: 0.5in; line-height: 1.5; margin-bottom: 10px;">
        <p style="margin: 0;">(Write names/s of respondent/s before made by which he/she/they was/were served)</p>
        <p style="margin: 0;"><span class="doc-field doc-field-short"></span> 1. Handing him/her/them said summon in person, or</p>
        <p style="margin: 0;"><span class="doc-field doc-field-short"></span> 2. Handing him/her/them said summon and he/she/they Refused to received it or</p>
        <p style="margin: 0;"><span class="doc-field doc-field-short"></span> 3. Leaving said summon at his/her/their dwelling with <span class="doc-field doc-field-medium"></span> <br><span style="margin-left:5in;">(Name)</span></p>
        <p style="margin: 0;"><span class="doc-field doc-field-short"></span> 4. Leaving said summons at his/her/their office/place of Business with <span class="doc-field doc-field-medium"></span> <br><span style="margin-left:5in;">(Name)</span></p>
    </div>

    <div style="float: right; text-align: center; width: 2.5in; margin-top: 5px;">
        <p style="margin: 0; font-weight: bold; font-size: 9pt;">Person in charge thereof:</p>
        <p style="margin: 1rem 0 0 0; font-weight: bold; width: 100%;"><span class="doc-field doc-field-full"></span></p>
        <p style="margin: 0;">Officer</p>
    </div>
    <div style="clear: both;"></div>

    <div style="margin-top: 15px;">
        <p style="margin-bottom: 0;">Received by respondent/s Representative/s:</p>
        <div style="display: flex; justify-content: space-between; margin-top: 15px; width: 6in;">
            <div style="width: 3.5in;"><span class="doc-field doc-field-full"></span></div>
            <div style="width: 2in;"><span class="doc-field doc-field-full"></span></div>
        </div>
        <div style="display: flex; justify-content: space-between; width: 6in; font-size: 9pt;">
            <div style="width: 3.5in; text-align: center;">Signature OVER PRINTED NAME</div>
            <div style="width: 2in; text-align: center;">Date</div>
        </div>
    </div>
</div>
@endsection

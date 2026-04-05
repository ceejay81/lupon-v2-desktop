@extends('documents.layout')

@section('doc-title', 'INVITATION NOTICE')

@section('content')
<div style="font-family: Calibri, sans-serif; font-size: 12pt;">
    <p style="line-height: 1.15; text-align: right; margin-bottom: 0.1in; font-weight: bold;">{{ date('F j, Y') }}</p>
    
    <div style="line-height: 1.15; text-align: left; margin-bottom: 0.1in; font-weight: bold;">
        <p style="margin: 0;"><span class="doc-field doc-field-medium" data-field="respondent">{{ strtoupper($case->respondentCitizen->full_name ?? $case->respondent ?? '') }}</span></p>
        <p style="margin: 0;"><span class="doc-field doc-field-long" data-field="respondent_address">{{ $case->respondentCitizen->address ?? '' }}</span></p>
        <p style="margin: 0;">{{ $settings['city_name'] ?? 'General Santos City' }}</p>
    </div>

    <p style="line-height: 1.15; text-align: left; margin-bottom: 0.1in; font-weight: bold; margin-top: 0.3in;">SIR/MADAM:</p>

    <p style="line-height: 1.15; text-align: justify; margin-bottom: 0.1in; text-indent: 0.5in;">
        May we respectfully invite your good presence of which you are principally concern for a dialogue relative to the complaint being brought to the office by 
        <span class="doc-field doc-field-medium" data-field="complainant">{{ $case->complainantCitizen->full_name ?? $case->complainant ?? '' }}</span>, 
        <span class="doc-field doc-field-long" data-field="complainant_address">{{ $case->complainantCitizen->address ?? '' }}</span> 
        on <span class="doc-field doc-field-medium"></span> <i>at <span class="doc-field-short doc-field"></span> in the <span class="doc-field doc-field-short"></span></i> at the Barangay {{ $settings['barangay_name'] ?? 'Bula' }} Office.
    </p>

    <p style="line-height: 1.15; text-align: justify; margin-bottom: 0.1in; text-indent: 0.5in;">
        Hoping for your positive response and presence on this matter. Please do not fail to attend and arrive on time.
    </p>

    <p style="line-height: 1.15; text-align: justify; margin-bottom: 0.2in; text-indent: 0.5in; color: #7030a0; font-style: italic; font-weight: bold; margin-top: 0.2in;">
        Observe proper dress code when attending the hearing. Kindly wear decent and appropriate attire as a sign of respect to the proceedings.
    </p>

    <p style="line-height: 1.15; text-align: justify; margin-bottom: 0.1in; text-indent: 0.5in;">
        Thank you and more power.
    </p>

    <p style="line-height: 1.15; text-align: center; margin-bottom: 0; font-weight: bold; margin-top: 0.3in;">Very truly yours,</p>

    <div style="width: 45%; text-align: center; margin-top: 0.5in; margin-left: auto;">
        <p style="line-height: 1.15; text-align: center; margin-bottom: 0; font-weight: bold;">{{ strtoupper($settings['punong_barangay'] ?? $settings['barangay_captain'] ?? 'HON. NICANORA T. VARGAS') }}</p>
        <p style="line-height: 1.15; text-align: center; margin-bottom: 0.1in; font-weight: bold;">Punong Barangay</p>
    </div>

    <div style="margin-top: 0.5in; font-weight: bold;">
        <p style="line-height: 1.15; text-align: left; margin-bottom: 0.05in;">Cc:</p>
        <p style="line-height: 1.15; text-align: left; margin-bottom: 0.05in;">Name of the Receiver: <span class="doc-field doc-field-medium"></span></p>
        <p style="line-height: 1.15; text-align: left; margin-bottom: 0.05in;">Date Received: <span class="doc-field doc-field-medium"></span></p>
        <p style="line-height: 1.15; text-align: left; margin-bottom: 0.05in;">Time: <span class="doc-field-short doc-field"></span></p>
        <p style="line-height: 1.15; text-align: left; margin-bottom: 0.05in;">Cellphone no. <span class="doc-field doc-field-medium"></span></p>
    </div>
</div>
@endsection

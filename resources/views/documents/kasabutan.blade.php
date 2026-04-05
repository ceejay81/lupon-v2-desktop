@extends('documents.layout')

@section('doc-title', 'KASABUTAN')

@section('content')
<div style="font-family: Arial, sans-serif;">
    <h2 style="text-align: center; font-size: 20pt; font-weight: bold; margin-bottom: 0.5in;">KASABUTAN</h2>

    <p style="text-align: right; font-weight: bold; font-size: 14pt; margin-bottom: 0.4in;">{{ date('F j, Y') }}</p>

    <p style="font-weight: bold; font-size: 14pt; margin-bottom: 0.5in;">KNOW BY ALL MEN THAT:</p>

    <div style="font-size: 14pt; line-height: 1.6; text-align: justify; margin-bottom: 1in;">
        <p style="text-indent: 0.5in;">Ako si <span class="doc-field doc-field-medium" data-field="respondent">{{ strtoupper($case->respondentCitizen->full_name ?? $case->respondent ?? '') }}</span> bayaran nako ang damage sa motor <b>Php <span class="doc-field doc-field-short"></span></b> karong bulan <span class="doc-field doc-field-medium"></span> diri sa barangay.</p>
        <p style="text-indent: 0.5in;">Ako si <span class="doc-field doc-field-medium" data-field="respondent">{{ strtoupper($case->respondentCitizen->full_name ?? $case->respondent ?? '') }}</span> nagsaad nga di na nako usbon ang padungog-dungog nga istorya.</p>
        <p style="text-indent: 0.5in;">Ako si <span class="doc-field doc-field-medium" data-field="complainant">{{ strtoupper($case->complainantCitizen->full_name ?? $case->complainant ?? '') }}</span> miuyon sa maong kasabutan.</p>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 1in; margin-top: 1in;">
        <div style="width: 45%; text-align: center;">
            <p style="margin: 0; font-weight: bold; font-size: 14pt;"><span class="doc-field doc-field-medium">{{ strtoupper($case->complainantCitizen->full_name ?? $case->complainant ?? '') }}</span></p>
            <p style="margin: 0; font-size: 12pt;">Complainant</p>
        </div>
        <div style="width: 45%; text-align: center;">
            <p style="margin: 0; font-weight: bold; font-size: 14pt;"><span class="doc-field doc-field-medium">{{ strtoupper($case->respondentCitizen->full_name ?? $case->respondent ?? '') }}</span></p>
            <p style="margin: 0; font-size: 12pt;">Respondent</p>
        </div>
    </div>

    <div style="text-align: center;">
        <p style="margin: 0; font-size: 14pt; font-weight: bold;">Mediator</p>
        <br/>
        <p style="margin: 0; font-weight: bold; font-size: 14pt;"><span class="doc-field doc-field-medium"></span></p>
    </div>
</div>
@endsection

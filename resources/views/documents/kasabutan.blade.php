@extends('documents.layout')

@section('doc-title', 'KASABUTAN')
@section('doc-type', 'Kasabutan')

@section('content')
    <div style="font-family: Arial, sans-serif;">
        <h2 style="text-align: center; font-size: 20pt; font-weight: bold; margin-bottom: 0.5in;">KASABUTAN</h2>

        <p style="text-align: right; font-weight: bold; font-size: 14pt; margin-bottom: 0.4in;">{{ date('F j, Y') }}</p>

        <p style="font-weight: bold; font-size: 14pt; margin-bottom: 0.5in;">KNOW BY ALL MEN THAT:</p>

        <div style="font-size: 14pt; line-height: 1.6; text-align: justify; min-height: 3in; margin-bottom: 1in;">
            <!-- Space for manual data entry by the Lupon -->
            <p>&nbsp;</p>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 1in; margin-top: 1in;">
            <div style="width: 45%; text-align: center;">
                <p style="margin: 0; font-weight: bold; font-size: 14pt;"><span
                        class="doc-field doc-field-medium">{{ strtoupper($case->complainants->pluck('name')->join(', ') ?: $case->complainant ?? '') }}</span>
                </p>
                <p style="margin: 0; font-size: 12pt;">Complainant</p>
            </div>
            <div style="width: 45%; text-align: center;">
                <p style="margin: 0; font-weight: bold; font-size: 14pt;"><span
                        class="doc-field doc-field-medium">{{ strtoupper($case->respondents->pluck('name')->join(', ') ?: $case->respondent ?? '') }}</span>
                </p>
                <p style="margin: 0; font-size: 12pt;">Respondent</p>
            </div>
        </div>

        <div style="text-align: center;">
            <p style="margin: 0; font-size: 14pt; font-weight: bold;">Mediator</p>
            <br />
            <p style="margin: 0; font-weight: bold; font-size: 14pt;"><span class="doc-field doc-field-medium"></span></p>
        </div>
    </div>
@endsection
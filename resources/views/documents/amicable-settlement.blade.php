@extends('documents.layout')

@section('doc-title', 'AMICABLE SETTLEMENT')

@section('content')
    <div style="font-family: Arial, sans-serif;">
        <!-- Header Block -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.1in;">
            <div style="width: 50%;">
                <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span
                        class="doc-field doc-field-medium" data-field="complainant">{{ strtoupper($case->complainantCitizen->full_name ?? $case->complainant ?? '') }}</span>
                </p>
                <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span
                        class="doc-field doc-field-long" data-field="complainant_address">{{ $case->complainantCitizen->address ?? '' }}</span></p>
                <p style="margin: 0; line-height: 1.15; font-size: 11pt;">
                    {{ $settings['city_name'] ?? 'General Santos City' }}
                </p>
                <p style="margin: 0; line-height: 1.15; font-size: 10pt;">===================</p>
                <p style="margin: 0; font-style: italic; font-size: 9pt; text-indent: 0.2in;">Complainant/s</p>
            </div>
            <div style="width: 35%;">
                <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 10pt;">Brgy. Case #: <span
                        class="doc-field-short doc-field" data-field="case_number">{{ $case->case_number ?? '' }}</span></p>
                <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 10pt; margin-top: 0.1in;">For: <span
                        class="doc-field doc-field-medium" data-field="nature_of_case">{{ $case->nature_of_complaint ?? $case->case_type ?? '' }}</span>
                </p>
            </div>
        </div>

        <p style="margin: 0.1in 0; text-indent: 0.5in; font-size: 10pt;">~against~</p>

        <div style="margin-bottom: 0.2in;">
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span
                    class="doc-field doc-field-medium" data-field="respondent">{{ strtoupper($case->respondentCitizen->full_name ?? $case->respondent ?? '') }}</span>
            </p>
            <p style="margin: 0; font-weight: bold; line-height: 1.15; font-size: 11pt;"><span
                    class="doc-field doc-field-long" data-field="respondent_address">{{ $case->respondentCitizen->address ?? '' }}</span></p>
            <p style="margin: 0; line-height: 1.15; font-size: 11pt;">{{ $settings['city_name'] ?? 'General Santos City' }}
            </p>
            <p style="margin: 0; line-height: 1.15; font-size: 10pt;">===================</p>
            <p style="margin: 0; font-style: italic; font-size: 9pt; text-indent: 0.2in;">Respondent/s</p>
        </div>

        <!-- Title -->
        <h2 style="text-align: center; font-size: 18pt; font-weight: bold; margin-top: 0.3in; margin-bottom: 0.2in;">
            AMICABLE SETTLEMENT</h2>

        <!-- Body -->
        <p style="text-indent: 0.5in; text-align: justify; line-height: 1.15; margin-bottom: 0.2in; font-size: 11pt;">We,
            complainant/s and respondent/s in the above captioned case; do hereby agree to settle our dispute as follows:
        </p>

        <ol
            style="margin-bottom: 0.4in; font-weight: bold; line-height: 1.5; text-align: justify; padding-left: 0.8in; font-size: 11pt;">
            <li style="margin-bottom: 0.2in;">Ako si <span
                    class="doc-field doc-field-medium">{{ strtoupper($case->respondentCitizen->full_name ?? $case->respondent ?? '') }}</span>,
                mangayo mi ug pasaylo sa among nabuhat ngadto kang <span
                    class="doc-field doc-field-medium">{{ strtoupper($case->complainantCitizen->full_name ?? $case->complainant ?? '') }}</span>.<br /><br />Ug
                among ulion ang among maayong kabubut-on isip usa ka pamilya.</li>
            <li>Ug ako si <span
                    class="doc-field doc-field-medium">{{ strtoupper($case->complainantCitizen->full_name ?? $case->complainant ?? '') }}</span>,
                akong gidawat ang ilang pagpangayo ug pasaylo sa ako.</li>
        </ol>

        <!-- Date -->
        <p style="line-height: 1.15; margin-bottom: 0.5in; text-indent: 0.5in; font-size: 11pt;">Entered into this <span
                class="doc-field doc-field-short">{{ date('jS') }}</span> day of <span
                class="doc-field doc-field-medium">{{ date('F Y') }}</span>.</p>

        <!-- Signatures -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5in;">
            <div style="width: 50%; text-align: center; box-sizing: border-box;">
                <p style="margin: 0; font-weight: bold; font-size: 11pt;"><span
                        class="doc-field doc-field-medium">{{ strtoupper($case->complainantCitizen->full_name ?? $case->complainant ?? '') }}</span>
                </p>
                <p style="margin: 0; font-size: 11pt; margin-top: 0.05in;">Complainant/s</p>
            </div>
            <div style="width: 35%; text-align: center; box-sizing: border-box;">
                <p style="margin: 0; font-weight: bold; font-size: 11pt;"><span
                        class="doc-field doc-field-medium">{{ strtoupper($case->respondentCitizen->full_name ?? $case->respondent ?? '') }}</span>
                </p>
                <p style="margin: 0; font-size: 11pt; margin-top: 0.05in;">Respondent/s</p>
            </div>
        </div>

        <!-- Attestation -->
        <h3 style="text-align: center; font-size: 12pt; font-weight: bold; margin-bottom: 0.2in;">ATTESTATION</h3>
        <p style="text-indent: 0.5in; text-align: justify; line-height: 1.15; margin-bottom: 0.5in; font-size: 11pt;">I
            hereby certify that the following amicable settlement was entered into by the parties freely and voluntarily,
            after I had explained to them the nature and consequence of such settlement.</p>

        <div style="width: 50%; margin: 0 auto; text-align: center;">
            <p style="margin: 0; font-weight: bold; font-size: 11pt;">
                {{ strtoupper($settings['punong_barangay'] ?? $settings['barangay_captain'] ?? 'HON. NICANORA T. VARGAS') }}
            </p>
            <p style="margin: 0; font-size: 11pt; margin-top: 0.05in;">Punong Barangay</p>
            <p style="margin: 0; font-size: 11pt;">LGU-Barangay {{ $settings['barangay_name'] ?? 'Bula' }}</p>
        </div>
    </div>
@endsection
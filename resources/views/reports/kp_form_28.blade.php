<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KP Form 28 - Monthly Transmittal of Final Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            margin: 0;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;    
        }
        .republic {
            font-size: 10pt;
            margin-bottom: 2px;
        }
        .province-city {
            font-size: 11pt;
            margin-bottom: 10px;
        }
        .barangay {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
        }
        .kp-form {
            position: absolute;
            top: 40px;
            left: 40px;
            font-weight: bold;
            font-size: 12pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            text-align: center;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            text-align: center;
            width: 250px;
        }
        .signature-line {
            border-bottom: 1px solid black;
            margin-bottom: 5px;
            height: 30px;
        }
        .to-block {
            margin-bottom: 20px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .print-btn {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn no-print">Print Report</button>

    <div class="kp-form">KP Form No. 28</div>

    <div class="header">
        <div class="republic">Republic of the Philippines</div>
        <div class="province-city">Province of Camarines Sur<br>City/Municipality of Bula</div>
        <div class="barangay">OFFICE OF THE LUPONG TAGAPAMAYAPA</div>
        <div>Barangay Bula</div>
    </div>

    <div class="title">MONTHLY TRANSMITTAL OF FINAL REPORTS</div>

    <div class="to-block">
        <strong>TO: The City/Municipal Judge</strong><br>
        (City/Municipality of Bula)
    </div>

    <p>Enclosed herewith are the final reports of settlement of disputes and arbitration awards made by the Punong Barangay/Pangkat Tagapagkasundo in the following cases:</p>

    <table>
        <thead>
            <tr>
                <th>Title of Case</th>
                <th>Nature of Case</th>
                <th>Date Settled/Awarded</th>
                <th>Status / Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cases as $case)
                <tr>
                    <td>{{ $case->complainant }} vs {{ $case->respondent }}<br><small>(Case No. {{ $case->case_number }})</small></td>
                    <td>{{ $case->nature_of_case }}</td>
                    <td>{{ $case->updated_at->format('M d, Y') }}</td>
                    <td>{{ ucfirst($case->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic;">No cases settled or dismissed for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p>For the month of <strong>{{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</strong>.</p>

    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line"></div>
            <strong>LUPON/PANGKAT SECRETARY</strong>
        </div>

        <div class="signature-block">
            <div style="text-align: left; margin-bottom: 20px;">Noted by:</div>
            <div class="signature-line"></div>
            <strong>PUNONG BARANGAY/LUPON CHAIRMAN</strong>
        </div>
    </div>

</body>
</html>

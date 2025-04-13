<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Employee Timesheet</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            width: 100%;
            margin-bottom: 15px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .employee-info {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 4px;
            border: none;
        }

        .info-label {
            font-weight: bold;
            width: 30%;
        }

        table.timesheet {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }

        .timesheet th,
        .timesheet td {
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
        }

        .timesheet th {
            background-color: #f2f2f2;
        }

        .checkbox {
            font-family: ZapfDingbats;
            font-size: 9px;
        }

        .totals {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 200px;
        }

        .signature-caption {
            width: 200px;
            margin-top: 5px;
            text-align: center;
        }

        .notes {
            margin-top: 20px;
            font-size: 10px;
        }

        .notes ul {
            padding-left: 15px;
        }

        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="header" cellpadding="0" cellspacing="0">
            <tr>
                <td width="70%">
                    <div class="title">Stundenaufzeichnungen laut Beitragsverfahrensverordnung</div>
                    <div class="subtitle">für sozialversicherungspflichtige Mitarbeiter</div>
                </td>
                <td width="30%">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="info-label">Monat / Jahr:</td>
                            <td>{{ $month }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-label">Name des Mitarbeiters:</td>
                <td>{{ $username ?? '' }}</td>
                <td class="info-label">Personalnummer:</td>
                <td>{{ $userId ?? '' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tätigkeitsbeschreibung/Projekt:</td>
                <td colspan="3">Gewährleistungs-/Garantiearbeiten</td>
            </tr>
        </table>

        <table class="timesheet">
            <thead>
                <tr>
                    <th rowspan="2">Tag</th>
                    <th rowspan="2">
                        ArbeitStunden</th>
                    <th colspan="2">Krank/Urlaub</th>
                    <th rowspan="2">
                        Soll-Arbeitsstd.</th>
                    <th rowspan="2">
                        Gesamtstunden</th>
                    <th colspan="6">davon</th>
                </tr>
                <tr>
                    <th>Krank</th>
                    <th>Urlaub</th>
                    <th>Normalarbeitszeit</p>
                    </th>
                    <th>Pausen</th>
                    <th>Schule / Uni</th>
                    <th>Überstunden</th>
                    <th>Krankheitsstunden
                    </th>
                    <th>Urlaubsstunden</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalOverTime = 0;
                @endphp
                @foreach ($summaries as $summary)
                                <tr>
                                    <td>{{ $summary->date->format('d') }}</td>
                                    <td>{{ roundToQuarter($summary->first_login, $summary->last_logout) }}</td>
                                    <td class="checkbox">
                                        @if ($summary->status && $summary->status->status == 'sick')
                                            <input checked type="checkbox">
                                        @else
                                            <input type="checkbox">
                                        @endif
                                    </td>
                                    <td class="checkbox">
                                        @if ($summary->status && $summary->status->status == 'off')
                                            <input checked type="checkbox">
                                        @else
                                            <input type="checkbox">
                                        @endif
                                    </td>
                                    <td>8</td>
                                    <td>9</td>
                                    <td>8</td>
                                    <td>1</td>
                                    <td>
                                        @if ($summary->status && $summary->status->status == 'school')
                                            <input checked type="checkbox">
                                        @else
                                            <input type="checkbox">
                                        @endif
                                    </td>
                                    <td>
                                        {{ $summary->over_time }} Stn
                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                @php
                                    $totalOverTime += $summary->over_time;
                                @endphp
                @endforeach
                <tr class="totals">
                    <td colspan="5"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $totalOverTime }}</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
            </tbody>
        </table>

        <div class="signature-section">
            <p>Mit meiner Unterschrift bestätige ich die Richtigkeit der vorstehenden Angaben:</p>

            <table width="100%">
                <tr>
                    <td width="50%">
                        <p>Ort, Datum: {{ $location_date ?? 'Aachen, ' . date('d.m.Y') }}</p>
                    </td>
                    <td width="50%">
                        <div class="signature-line"></div>
                        <div class="signature-caption">Unterschrift</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="notes">
            <p><strong>Hinweis:</strong></p>
            <ul>
                <li>Krankmeldungen sind gemäß § 5 (1) EFZG unverzüglich, d.h. am Tage der Krankmeldung, spätestens am
                    folgenden Tage vorzulegen. Bei Nichtvorlage ist der Arbeitgeber gemäß § 7 EFZG berechtigt, die
                    Entgeltfortzahlung zu verweigern.</li>
                <li>Dieser Stundennachweis ist spätestens am 3. Arbeitstag für den Vormonat vorzulegen.</li>
                <li>Zum 5. letzten Arbeitstag im Monat ist für Zwecke der SV-Beitragsschätzung ein vorläufiger
                    Stundennachweis einzureichen.</li>
                <li>Überstunden können nur nach Genehmigung geleistet werden.</li>
            </ul>
        </div>

        <div class="footer">
            <p>Formular Version: 009 | erstellt/bearbeitet: TobEs 2/28/2014 | freigegeben: ThoWi 5/10/2012</p>
        </div>
    </div>
</body>

</html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Safety Walk and Talk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        .status-badge {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        .foto-masalah {
            max-width: 200px;
            height: auto;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo">
        <div class="title">LAPORAN SAFETY WALK AND TALK</div>
        <div class="subtitle">Periode: {{ $periode }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="8%">Tanggal Laporan</th>
                <th width="8%">Tanggal Selesai</th>
                <th width="15%">Departemen</th>
                <th width="15%">Kategori</th>
                <th width="25%">Masalah</th>
                <th width="25%">Penyelesaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($item->penyelesaian && $item->penyelesaian->Tanggal)
                        {{ \Carbon\Carbon::parse($item->penyelesaian->Tanggal)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->departemenSupervisor->departemen }}<br>
                    <small>{{ $item->departemenSupervisor->supervisor }}</small>
                </td>
                <td>{{ $item->kategori_masalah }}</td>
                <td>
                    {{ $item->deskripsi_masalah }}
                    @if($item->Foto)
                        <br><img src="{{ public_path('images/'.$item->Foto) }}" class="foto-masalah">
                    @endif
                </td>
                <td>
                    @if($item->penyelesaian)
                        {{ $item->penyelesaian->deskripsi_penyelesaian }}
                        @if($item->penyelesaian->Foto)
                            <br><img src="{{ public_path('images/'.$item->penyelesaian->Foto) }}" class="foto-masalah">
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Lulusan UNLA</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            bg-color: #1e3a8a;
            color: white;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>DAFTAR LULUSAN UNIVERSITAS LANGLANGBUANA</h2>
        <p>Fakultas: {{ $metadata['fakultas'] }} | Tahun: {{ $metadata['tahun'] }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>NIM</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>IPK</th>
                <th>Lama Studi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{ $d->nim }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ $d->prodi }}</td>
                <td>{{ number_format($d->ipk, 2) }}</td>
                <td>{{ $d->lama_studi }} Sem</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
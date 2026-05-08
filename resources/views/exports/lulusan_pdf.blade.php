<div class="meta-box">
    <table style="width: 100%; font-size: 11px;">
        <tr>
            <td style="font-weight: bold; width: 100px;">Fakultas</td>
            <td>: {{ $metadata['fakultas'] }}</td>
            <td style="font-weight: bold; width: 100px;">Dicetak Pada</td>
            <td>: {{ $metadata['tanggal'] }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Prodi</td>
            <td>: {{ $metadata['prodi'] }}</td>
            <td style="font-weight: bold;">Total Data</td>
            <td>: {{ $metadata['total'] }} Mahasiswa</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Periode</td>
            <td>: {{ $metadata['periode'] }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>

<table class="main-table" style="width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 20px;">
    <thead>
        <tr style="background-color: #1e3a8a; color: white;">
            <th style="padding: 10px;">No</th>
            <th style="padding: 10px;">Nama Mahasiswa</th>
            <th style="padding: 10px;">NIM</th>
            <th style="padding: 10px;">Lama Studi</th>
            <th style="padding: 10px;">IPK</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $mhs)
        <tr>
            <td style="text-align: center; padding: 8px; border-bottom: 1px solid #eee;">{{ $index + 1 }}</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">{{ strtoupper($mhs->nama) }}</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $mhs->nim }}</td>
            <td style="text-align: center; padding: 8px; border-bottom: 1px solid #eee;">
                <span style="color: {{ $mhs->lama_studi <= 9 ? '#1e3a8a' : '#ef4444' }}; font-weight: bold;">
                    {{ $mhs->lama_studi }} Semester
                </span>
            </td>
            <td style="text-align: center; padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">
                {{ number_format($mhs->ipk, 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
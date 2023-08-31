{{-- <!DOCTYPE html>
<html>
<head>
    <title>Hasil Pemindaian KTP</title>
</head>
<body>
    <h1>Hasil Pemindaian KTP</h1>

    <table>
        <tr>
            <td>Nama:</td>
            <td>{{ $data['name'] }}</td>
        </tr>
        <tr>
            <td>NIK:</td>
            <td>{{ $data['nik'] }}</td>
        </tr>
        <tr>
            <td>Tempat Tanggal Lahir:</td>
            <td>{{ $data['birth_place_date'] }}</td>
        </tr>
        <tr>
            <td>Alamat:</td>
            <td>{{ $data['address'] }}</td>
        </tr>
        <tr>
            <td>RT/RW:</td>
            <td>{{ $data['rt_rw'] }}</td>
        </tr>
        <tr>
            <td>Golongan Darah:</td>
            <td>{{ $data['blood_type'] }}</td>
        </tr>
    </table>
</body>
</html> --}}
<!DOCTYPE html>
<html>
<head>
    <title>Hasil Pemindaian KTP</title>
</head>
<body>
    <h1>Hasil Pemindaian KTP</h1>
    <pre>{{ $result }}</pre>
</body>
</html>

{{-- <!DOCTYPE html>
<html>
<head>
    <title>Hasil Scan KTP</title>
    <style>
        .scan-result {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #333;
            line-height: 1.5;
        }
        .scan-result-item {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Hasil Scan KTP:</h1>
    <pre class="scan-result">
        <span class="scan-result-item">{{ $nama }}</span>
        <span class="scan-result-item">{{ $nik }}</span>
        <span class="scan-result-item">{{ $tgl }}</span>
        {{-- <span class="scan-result-item">{{ $alm }}</span>
        <span class="scan-result-item">{{ $rt }}</span> --}}
<!-- Tambahkan bagian-bagian lain dari hasil scan KTP di sini -->
{{-- </pre>
</body>

</html>  --}}

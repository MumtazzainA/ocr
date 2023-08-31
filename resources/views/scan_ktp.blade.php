<!DOCTYPE html>
<html>
<head>
    <title>Scan KTP</title>
</head>
<body>
    <form action="/scan-ktp" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="ktp_image">
        <button type="submit">Scan KTP</button>
    </form>
</body>
</html>

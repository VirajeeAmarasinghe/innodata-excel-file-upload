<!DOCTYPE html>
<html>

<head>
    <title>Import CSV/Excel File</title>
</head>

<body>

    <h2>Import Users</h2>
    <form action="/import" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import</button>
    </form>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

</body>

</html>
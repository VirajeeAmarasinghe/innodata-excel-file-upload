<!DOCTYPE html>
<html>

<head>
    <title>Excel Import</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th a {
            text-decoration: none;
            color: black;
        }

        .status {
            margin-top: 10px;
            padding: 6px;
        }

        .ok {
            background: #dff0d8;
            color: green;
        }

        .warn {
            background: #fcf8e3;
            color: #8a6d3b;
        }

        .errors {
            background: #f2dede;
            padding: 6px;
            margin-top: 10px;
        }

        /* Pagination */
        nav[role="navigation"] {
            margin-top: 15px;
            text-align: center;
        }

        nav[role="navigation"] a,
        nav[role="navigation"] span {
            display: inline-block;
            margin: 0 3px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #333;
        }

        nav[role="navigation"] [aria-current="page"] span {
            background: #007bff;
            color: #fff;
        }

        nav[role="navigation"] [aria-disabled="true"] span {
            color: #999;
            background: #eee;
        }
    </style>
</head>

<body>
    <h2>Upload Excel</h2>
    <form action="{{ route('import.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    @if(session('status'))
        <div class="status {{ session('import_errors') ? 'warn' : 'ok' }}">{{ session('status') }}</div>
    @endif

    @if(session('import_errors'))
        <div class="errors">
            <strong>Errors:</strong>
            <ul>
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>Imported Users</h2>
    <table>
        <thead>
            <tr>
                <th><a href="?sort=name&dir={{ $dir === 'asc' ? 'desc' : 'asc' }}">Name</a></th>
                <th><a href="?sort=email&dir={{ $dir === 'asc' ? 'desc' : 'asc' }}">Email</a></th>
                <th><a href="?sort=contact_number&dir={{ $dir === 'asc' ? 'desc' : 'asc' }}">Contact Number</a></th>
                <th><a href="?sort=address&dir={{ $dir === 'asc' ? 'desc' : 'asc' }}">Address</a></th>
                <th><a href="?sort=birthday&dir={{ $dir === 'asc' ? 'desc' : 'asc' }}">Birthday</a></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->contact_number }}</td>
                    <td>{{ $user->address }}</td>
                    <td>{{ $user->birthday }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</body>

</html>
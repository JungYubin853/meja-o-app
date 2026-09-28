<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $filename }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 24px; color: #1e293b; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        p { color: #64748b; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background-color: #f1f5f9; padding: 8px; text-align: left; border-bottom: 2px solid #cbd5e1; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0f172a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
            Save / Print PDF
        </button>
    </div>

    <h1>My Kopi-O Group — {{ ucfirst($view) }} Visitor Report</h1>
    <p>Generated on {{ now()->format('d M Y, H:i') }} | Total Records: {{ count($logs) }}</p>

    <table>
        <thead>
            <tr>
                <th>Log ID</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Pax</th>
                <th>Started</th>
                <th>Ended</th>
                <th>Elapsed</th>
                <th>Staff</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>#{{ $log->id }}</td>
                    <td>{{ $log->customer_name ?? 'Walk-in Guest' }}</td>
                    <td>{{ $log->phone ?? '-' }}</td>
                    <td>{{ $log->pax }}</td>
                    <td>{{ $log->started_at ? \Carbon\Carbon::parse($log->started_at)->format('d M Y, H:i') : '-' }}</td>
                    <td>{{ $log->ended_at ? \Carbon\Carbon::parse($log->ended_at)->format('d M Y, H:i') : 'In Progress' }}</td>
                    <td>{{ $log->time_elapsed ?? '-' }}</td>
                    <td>{{ $log->created_by ?? 'System' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Reports</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { text-align: center; color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8f9fa; padding: 8px; text-align: left; border: 1px solid #dee2e6; font-weight: bold; }
        td { padding: 6px; border: 1px solid #dee2e6; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .header { text-align: center; margin-bottom: 30px; }
        .date { text-align: center; color: #666; margin-bottom: 20px; }
        .status-badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .status-available { background-color: #d4edda; color: #155724; }
        .status-low { background-color: #fff3cd; color: #856404; }
        .status-out { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Reports</h1>
    </div>
    <div class="date">
        Generated on: {{ now()->format('F d, Y') }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Stocks Used</th>
                <th>Status</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventory as $item)
            @php
                $stocksUsed = $item->transactions
                    ->where('transaction_type', '!=', 'restock')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('quantity');
            @endphp
            <tr>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                <td>{{ $stocksUsed }} {{ $item->unit }}</td>
                <td>
                    <span class="status-badge status-{{ $item->status }}">
                        {{ str_replace('_', ' ', ucfirst($item->status)) }}
                    </span>
                </td>
                <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('M d, Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 10px;">
        Total Items: {{ $inventory->count() }}
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            background: #f4f7f6;
            padding: 20px;
            color: #111;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid #ddd;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .subtitle {
            color: #777;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8f8f8;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 18px;
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #777;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
        }

        .components-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f3f3f3;
        }

        th {
            text-align: left;
            padding: 14px;
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
        }

        td {
            padding: 14px;
            border-top: 1px solid #eee;
            vertical-align: middle;
        }

        .component-name {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>
<div class="card">
    <div class="title"><?= htmlspecialchars($pc_name ?? 'PC Build') ?></div>
    <div class="subtitle">Generated PC configuration specification</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Price</div>
            <div class="stat-value"><?= money($total_price ?? 0) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Mean Quality</div>
            <div class="stat-value"><?= $mean_quality ?? '—' ?>/10</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Components Count</div>
            <div class="stat-value"><?= count($components ?? []) ?></div>
        </div>
    </div>
    <div class="components-title">Included Components</div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (($components ?? []) as $comp): ?>
            <tr>
                <td>
                    <div class="component-name"><?= htmlspecialchars($comp['name']) ?></div>
                </td>
                <td><?= htmlspecialchars($comp['brand'] ?? '—') ?></td>
                <td>$<?= number_format($comp['price'], 2) ?></td>
                <td><?= (int)($comp['quantity'] ?? 1) ?></td>
                <td>
                    $<?= number_format(
                        ($comp['price'] ?? 0) * ($comp['quantity'] ?? 1),
                        2
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f4f7f6;
            padding: 20px;
            color: #1a1f36;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            max-width: 850px;
            margin: 0 auto;
            border: 1px solid #eef2f5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #111;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .filter-badge {
            display: inline-block;
            padding: 6px 14px;
            background: #f0f4f8;
            border: 1px solid #dcdfe4;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background: #f8fafc;
        }

        th {
            text-align: left;
            padding: 14px;
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            border-bottom: 2px solid #edf2f7;
        }

        td {
            padding: 16px 14px;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
            vertical-align: top;
        }

        .config-name {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .meta-line {
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 3px;
        }
        
        .meta-line strong {
            color: #1e293b;
        }

        .brands-list {
            font-size: 12px;
            color: #64748b;
            font-style: italic;
        }

        .price-text {
            font-size: 16px;
            font-weight: 700;
            color: #059669;
            white-space: nowrap;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="title">PC Configurations Report</div>
    <div class="subtitle">Generated list of available builds from PC Store</div>

    <?php if (!empty($searchQuery)): ?>
        <div class="filter-badge">
            Active Search Filter: <strong>"<?= htmlspecialchars($searchQuery) ?>"</strong>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Configuration Details</th>
                <th style="width: 120px; text-align: right;">Price</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($configurations)): ?>
            <tr>
                <td colspan="2" style="text-align: center; color: #94a3b8; padding: 40px 0;">
                    No configurations match the current criteria on this page.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($configurations as $configItem): ?>
                <?php 
                if (!($configItem instanceof PcConfiguration)) continue;
                $name  = $configItem->name;
                $brand = $configItem->getBrand();
                $price = $configItem->getPrice();
                $components = $configItem->components ?? [];
                $compBrands = [];
                $qualitySum = 0;
                $qualityCount = 0;
                foreach ($components as $component) {
                    $compArray = is_object($component) ? (array)$component : $component;
                    if (!empty($compArray['brand'])) $compBrands[] = trim($compArray['brand']);
                    if (isset($compArray['quality']) && $compArray['quality'] !== '') {
                        $qualitySum += (float)$compArray['quality'];
                        $qualityCount++;
                    }
                }

                $uniqueBrands = array_unique($compBrands);
                $brandsList   = !empty($uniqueBrands) ? implode(', ', $uniqueBrands) : '—';
                $avgQuality   = $qualityCount > 0 ? round($qualitySum / $qualityCount, 1) : '—';

                if (empty($components) && $price > 0) $brandsList = $brand; 
                ?>
                <tr>
                    <td>
                        <div class="config-name"><?= htmlspecialchars($name) ?></div>
                        
                        <div class="meta-line">
                            <strong>Brand:</strong> <?= htmlspecialchars($brand) ?> | 
                            <strong>Avg Quality:</strong> <?= $avgQuality ?><?= $qualityCount > 0 ? ' / 10' : '' ?>
                        </div>
                        
                        <div class="brands-list">
                            <strong>Components:</strong> <?= htmlspecialchars($brandsList) ?>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <span class="price-text">
                            <?= function_exists('money') ? money($price) : '$' . number_format($price, 2) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        © <?= date('Y') ?> PC Store. Total items on this page: <?= count($configurations) ?>.
    </div>
</div>
</body>
</html>
<?php
require_once __DIR__ . '/../shared/_helpers.php';
require_once __DIR__ . '/../shared/_data_base.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$raw = trim($_GET['q'] ?? '');

if ($raw === '') {
    unset($_SESSION['search_config_ids']);
    unset($_SESSION['search_query_string']);
    header('Location: index.php?page=pc_list');
    exit;
}

$filters = [];
$textQuery = preg_replace_callback(
    '/(quality|price):(>=|<=|>|<|=)([0-9.]+)/i',
    function (array $m) use (&$filters): string {
        $filters[] = [
            'key' => strtolower($m[1]),
            'op'  => $m[2],
            'val' => (float) $m[3],
        ];
        return '';
    },
    $raw
);
$textQuery = trim($textQuery);

$whereClause = '';
$params      = [];

if ($textQuery !== '') {
    $escapedQuery = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $textQuery);
    $like         = '%' . $escapedQuery . '%';
    $whereClause  = 'WHERE (c.name LIKE ? OR comp.brand LIKE ? OR comp.type LIKE ?)';
    $params       = [$like, $like, $like];
}

$havingParts  = [];
$havingParams = [];
$allowedOps = ['>', '<', '>=', '<=', '='];
foreach ($filters as $f) {
    if (!in_array($f['op'], $allowedOps, true)) continue;
    $col            = $f['key'] === 'quality' ? 'avg_quality' : 'total_price';
    $havingParts[]  = "$col {$f['op']} ?";
    $havingParams[] = $f['val'];
}

$havingClause = !empty($havingParts) ? 'HAVING ' . implode(' AND ', $havingParts) : '';

$sql = "
    SELECT c.id,
           COALESCE(SUM(comp.price * comp.quantity), 0) AS total_price,
           COALESCE(ROUND(AVG(comp.quality), 1), 0) AS avg_quality
    FROM pc_configurations c
    LEFT JOIN pc_components comp ON comp.pc_configuration_id = c.id
    $whereClause
    GROUP BY c.id
    $havingClause
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($params, $havingParams));
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $_SESSION['search_config_ids'] = !empty($ids) ? $ids : [-1];
    $_SESSION['search_query_string'] = $raw;
    header('Location: index.php?page=pc_list');
    exit;

} catch (PDOException $e) {
    die("Search execution failed: " . $e->getMessage());
}
<?php
require_once __DIR__ . '/shared/_helpers.php';
require_once __DIR__ . '/shared/_auth.php';
require_once __DIR__ . '/shared/_data_base.php';
require_once __DIR__ . '/shared/_pc_conf_service.php';

$page = $_GET['page'] ?? 'home';
$p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pageContent = '';
$pageScripts = '';

$configService = new PcConfigurationService($pdo);
$action = $_GET['action'] ?? '';
$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

switch ($action) {
    case 'save_pc_configuration' :
        require_once __DIR__ . '/pc_configuration/save_pc_configuration.php'; 
        exit;
    case 'search_configurations' :
        require_once __DIR__ . '/pc_configurations/search.php'; 
        exit;
    case 'removePcConfiguration' :
        header('Content-Type: application/json');
        $success = $configService->removePcConfiguration($id);
        echo json_encode(['success' => $success]);
        exit;
}

switch ($page) {
case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') login(); 
        require_once __DIR__ . '/auth/login.php';
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') register(); 
        require __DIR__ . '/auth/register.php';
        break;

    case 'admin_panel':
        if (isInRole(UserRole::Admin)) {
            require __DIR__ . '/admin_panel/index.php';
        } else { 
            ob_start(); echo "<h2>Access denied</h2>"; $pageContent = ob_get_clean(); 
        }
        break;

    case 'profile':
        require __DIR__ . '/profile/index.php';
        break;

    case 'pc_configuration':
        $config = $configService->getPcConfiguration($id);
        require __DIR__ . '/pc_configuration/index.php';
        break;
    case 'pc_configuration_edit':
        $config = $configService->getPcConfiguration($id);
        require __DIR__ . '/pc_configuration/edit.php';
        break;
    case 'home':
    case 'pc_list':
    default:
        require_once __DIR__ . '/models/pc_configuration.php';
        if (session_status() === PHP_SESSION_NONE) session_start();

        $currentPage = (int)($_GET['p'] ?? 1);
        $perPage = isAdminOrStaff() ? 29 : 30;
        $offset = max(0, ($currentPage - 1) * $perPage);

        $searchIds = $_SESSION['search_config_ids'] ?? null;
        $searchQuery = $_SESSION['search_query_string'] ?? '';

        if ($searchIds !== null) {
            $totalCount = (count($searchIds) === 1 && $searchIds[0] === -1) ? 0 : count($searchIds);
            $totalPages = max(1, (int)ceil($totalCount / $perPage));

            if ($totalCount > 0) {
                $pageIds = array_slice($searchIds, $offset, $perPage);
                $inQuery = implode(',', array_fill(0, count($pageIds), '?'));

                $sql = "SELECT * FROM pc_configurations WHERE id IN ($inQuery) ORDER BY id DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->setFetchMode(PDO::FETCH_CLASS, 'PcConfiguration');
                $stmt->execute($pageIds);
                $configurations = $stmt->fetchAll();
            } else $configurations = [];
        } else {
            $totalCount = $pdo->query("SELECT COUNT(*) FROM pc_configurations")->fetchColumn();
            $totalPages = max(1, (int)ceil($totalCount / $perPage));

            $sql = "SELECT * FROM pc_configurations ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'PcConfiguration');
            $stmt->execute();
            $configurations = $stmt->fetchAll();
        }

        if (!empty($configurations)) {
            $configIds = array_map(fn($item) => $item->id, $configurations);
            $placeholders = implode(',', array_fill(0, count($configIds), '?'));
            
            $compStmt = $pdo->prepare("SELECT * FROM pc_components WHERE pc_configuration_id IN ($placeholders)");
            $compStmt->execute($configIds);
            $allComponents = $compStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($allComponents as $component) {
                $configId = $component['pc_configuration_id'];
                foreach ($configurations as $config) {
                    if ($config->id === $configId) {
                        $config->components[] = $component;
                        break;
                    }
                }
            }
        }

        $paginationData = [
            'has_pages' => $totalPages > 1,
            'total'     => $totalPages,
            'current'   => $currentPage
        ];

        require __DIR__ . '/pc_configurations/index.php';
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES) ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/site.css">
    <title>PC Store</title>
</head>
<body>
    <header>
        <?php include __DIR__ . '/_header_panel.php'; ?>
    </header>

    <div class="center-container">
        <?= $pageContent ?>
    </div>

    <footer class="border-top footer text-muted">
        <div class="glass-container">
            <div>&copy; 2026 - PC Store</div>
        </div>
    </footer>

    <?= $pageScripts ?>
</body>
</html>

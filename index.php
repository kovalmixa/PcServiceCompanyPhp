<?php
require_once __DIR__ . '/shared/_helpers.php';
require_once __DIR__ . '/shared/_auth.php';
require_once __DIR__ . '/shared/_data_base.php';
require_once __DIR__ . '/shared/_pc_list_service.php';

$action = $_GET['action'] ?? '';
if ($action === 'save_pc_configuration') {
    require_once __DIR__ . '/pc_configuration/save_pc_configuration.php'; 
    exit;
}
$page = $_GET['page'] ?? 'home';
$p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pageContent = '';
$pageScripts = '';

$configService = new PcConfigurationService($pdo);

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

        $currentPage = (int)($_GET['p'] ?? 1);
        $perPage = isAdminOrStaff() ? 29 : 30;
        $offset = max(0, ($currentPage - 1) * $perPage);

        $totalItems = $pdo->query("SELECT COUNT(*) FROM pc_configurations")->fetchColumn();
        $totalPages = max(1, (int)ceil($totalItems / $perPage));

        $stmt = $pdo->prepare("SELECT * FROM pc_configurations ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'PcConfiguration');
        $stmt->execute();
        
        $configurations = $stmt->fetchAll();

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

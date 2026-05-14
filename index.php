<?php
require_once __DIR__ . '/shared/_helpers.php';
require_once __DIR__ . '/shared/_auth.php';

$page = $_GET['page'] ?? 'home';
$pageContent = '';
$pageScripts = '';

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
        if (isInRole(UserRole::Admin)) require __DIR__ . '/admin_panel/index.php';
        else { ob_start(); echo "<h2>Access denied</h2>"; $pageContent = ob_get_clean(); }
        break;
    case 'profile':
        require __DIR__ . '/profile/index.php';
        break;
    case 'pc_configuration':
        require __DIR__ . '/pc_configuration/index.php';
        break;
    case 'pc_configuration_edit':
        require __DIR__ . '/pc_configuration/edit.php';
        break;
    case 'home':
    case 'pc_list':
    default:
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

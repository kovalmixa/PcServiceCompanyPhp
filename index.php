<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES) ?>">
    <link rel="stylesheet" href="css/site.css">
    <title>PC Store</title>
</head>
<body>
    <header>
        <?php include __DIR__ . '/_header_panel.php'; ?>
    </header>

    <div class="center-container">
        <?= $pageContent ?? '' ?>
    </div>

    <footer class="border-top footer text-muted">
        <div class="glass-container">
            <div>
                &copy; 2026 - PC Store - <a href="/privacy">Privacy</a>
            </div>
        </div>
    </footer>
    <?= $pageScripts ?? '' ?>
</body>
</html>

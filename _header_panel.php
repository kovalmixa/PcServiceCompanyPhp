<?php 
require_once __DIR__ . '/shared/_auth.php';
require_once __DIR__ . '/shared/_helpers.php';
?>

<div class="glass-container">
    <div class="header-panel">
        <div class="logo" style="display: flex; align-items: center; gap: 20px;">PC Store</a></div>

        <nav class="row-container" style="justify-content: flex-end; margin-right: 0;">
            <?php if (isAuthenticated()): ?>
                <a class="a-btn" href="<?= BASE_URL ?>pc_configurations/index.php">Profile</a>
            <?php endif; ?>
            <a class="a-btn" href="<?= BASE_URL ?>pc_configurations/index.php">PC Configurations</a>

            <?php if (isInRole(UserRole::Admin)): ?>
                <a class="a-btn" href="<?= BASE_URL ?>admin_panel/admin_panel.php">Admin Panel</a>
            <?php endif; ?>

            <?php if (!isAuthenticated()): ?>
                <a class="a-btn" href="<?= BASE_URL ?>auth/login.php">Login</a>
                <a class="a-btn" href="<?= BASE_URL ?>auth/register.php">Register</a>
            <?php else: ?>
                <a class="a-btn">Logout</a>
            <?php endif; ?>
        </nav>
    </div>
</div>
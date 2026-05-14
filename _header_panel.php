<?php require_once __DIR__ . '/shared/_helpers.php'; ?>
<div class="glass-container">
    <div class="header-panel">
        <div class="logo" style="display:flex;align-items:center;gap:20px;">
            <a href="<?= BASE_URL ?>index.php?page=home">PC Store</a>
        </div>

        <nav class="row-container" style="justify-content:flex-end;margin-right:0;">
            <?php if (isAuthenticated()): ?>
                <a class="a-btn" href="<?= BASE_URL ?>index.php?page=profile">Profile</a>
            <?php endif; ?>

            <a class="a-btn" href="<?= BASE_URL ?>index.php?page=pc_list">PC Configurations</a>

            <?php if (isInRole(UserRole::Admin)): ?>
                <a class="a-btn" href="<?= BASE_URL ?>index.php?page=admin_panel">Admin Panel</a>
            <?php endif; ?>

            <?php if (!isAuthenticated()): ?>
                <a class="a-btn" href="<?= BASE_URL ?>index.php?page=login">Login</a>
                <a class="a-btn" href="<?= BASE_URL ?>index.php?page=register">Register</a>
            <?php else: ?>
                <a class="a-btn" href="<?= BASE_URL ?>index.php?action=logout">Logout</a>
            <?php endif; ?>
        </nav>
    </div>
</div>

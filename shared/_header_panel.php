<?php
require_once __DIR__ . '/_helpers.php';

$pageSize       = isInRole('Admin') ? 29 : 30;
$balanceLabel   = isInRole('Customer') ? 'Personal Balance' : 'Company Balance';
$balance        = $balance ?? 0.00;
?>

<div class="glass-container">
    <div class="header-panel">

        <div class="logo" style="display: flex; align-items: center; gap: 20px;">
            <a href="/pc-configurations?pageSize=<?= $pageSize ?>">PC Store</a>

            <?php if (isAuthenticated()): ?>
                <div class="balance-wrapper" style="display: flex; flex-direction: column; line-height: 1.1;">
                    <span style="font-size: 0.7rem; color: rgba(0,0,0,0.5); text-shadow: none;">
                        <?= e($balanceLabel) ?>
                    </span>
                    <span id="header-balance" class="money" style="font-size: 1.4rem;">
                        <?= money($balance) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <nav class="row-container" style="justify-content: flex-end; margin-right: 0;">
            <?php if (isAuthenticated()): ?>

                <a href="/client/profile" class="a-btn">Profile</a>

                <?php if (isInRole('Customer')): ?>
                    <a href="/client/orders/cart" class="a-btn">Cart</a>
                <?php endif; ?>

                <?php if (isInRole('Admin')): ?>
                    <a href="/staff/component-order" class="a-btn">Orders</a>
                <?php else: ?>
                    <a href="/client/orders" class="a-btn">Orders</a>
                <?php endif; ?>

                <a href="/pc-configurations?pageSize=<?= $pageSize ?>" class="a-btn">PC Configurations</a>
                <a href="/components?pageSize=<?= $pageSize ?>" class="a-btn">Components</a>

                <?php if (isInRole('Staff') || isInRole('Admin')): ?>
                    <button onclick="downloadReport()" class="a-btn">Log Reports</button>
                <?php endif; ?>

                <?php if (isInRole('Admin')): ?>
                    <a href="/admin/suppliers" class="a-btn">Suppliers</a>
                    <a href="/admin/panel" class="a-btn">Admin Panel</a>
                <?php endif; ?>

                <a href="/auth/logout" class="red-btn">Logout</a>

            <?php else: ?>

                <a href="/pc-configurations?pageSize=30" class="a-btn">PC Configurations</a>
                <a href="/components?pageSize=30" class="a-btn">Components</a>
                <a href="/auth/login" class="a-btn">Login</a>
                <a href="/auth/register" class="a-btn">Register</a>

            <?php endif; ?>
        </nav>

    </div>
</div>

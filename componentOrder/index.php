<?php
/**
 * Component Orders – index view
 * Equivalent of Views/ComponentOrder/Index.cshtml
 *
 * Expected: $orders  (array of ComponentOrderRow-like associative arrays)
 *           $_SESSION['csrf_token'] set by the router/controller
 */

$layout = __DIR__ . '/../Shared/_layout.php';
$pageTitle = 'Component Orders';

ob_start(); // capture page body for layout injection
?>

<link rel="stylesheet" href="/css/grid.css">

<div style="max-width: 1480px; margin: 0 auto; padding: 0 20px;">
    <div class="glass-container" style="margin-bottom: 20px; padding: 16px 24px;">
        <h2 style="margin: 0;">Component Orders</h2>
    </div>

    <?php if (empty($orders)): ?>
        <div class="glass-container" style="text-align: center; padding: 40px; opacity: 0.7;">
            <p style="margin: 0; font-size: 1.1rem;">No pending orders.</p>
        </div>
    <?php else: ?>
        <div id="orders-container">
            <?php foreach ($orders as $order): ?>
                <?php include __DIR__ . '/_component_row.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/../Shared/_pagination.php'; ?>
</div>

<!-- CSRF token field (read by data-base.js) -->
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

<script type="module">
    import { sendActionRequest } from '/js/data-base.js';
    import { updatePriceLabel } from '/js/price.js';

    function handleSupplierChange(selectElement, labelId, basePrice) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const multiplier = parseFloat(selectedOption.getAttribute('data-multiplier')) || 1;

        const newTotal = multiplier * basePrice;
        updatePriceLabel(labelId, newTotal);
    }

    async function handleSupply(orderId, rowEl) {
        const select = rowEl.querySelector('.supplier-select');
        const supplierId = parseInt(select?.value ?? '0');

        if (!supplierId || supplierId <= 0) {
            select.style.borderColor = '#555';
            select.title = 'Please select a supplier first';
            alert('Please select a supplier before confirming the supply.');
            return;
        }

        const btn = rowEl.querySelector('button');
        btn && (btn.disabled = true);

        const response = await sendActionRequest(
            '/staff/component-order/supply',
            'POST',
            { orderId, supplierId }
        );
        const result = await response.json();
        if (result.success) {
            const balanceElement = document.getElementById('header-balance');
            if (balanceElement) balanceElement.innerText = `$${result.newBalance}`;
            rowEl.remove();
        }
    }

    async function handleCancel(orderId, rowEl) {
        await sendActionRequest(
            '/staff/component-order/cancel',
            'POST',
            { orderId }
        );
        rowEl.remove();
    }

    window.handleSupplierChange = handleSupplierChange;
    window.handleSupply = handleSupply;
    window.handleCancel = handleCancel;
</script>

<?php
$pageContent = ob_get_clean();
include $layout;
?>

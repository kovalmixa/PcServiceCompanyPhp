<?php
/**
 * Component Order Row – partial view
 * Equivalent of Views/ComponentOrder/_ComponentRow.cshtml
 *
 * Expected: $order (associative array) with keys:
 *   'id'        => int
 *   'item_card' => [
 *       'price'    => float,
 *       'quantity' => int,
 *       (+ any keys expected by _item_card.php)
 *   ]
 *   'suppliers' => [ ['id' => int, 'company_name' => string, 'price_multiplier' => float], ... ]
 */

$id          = (int)  $order['id'];
$itemCard    = $order['item_card'];
$suppliers   = $order['suppliers'] ?? [];
$totalPrice  = $itemCard['price'] * $itemCard['quantity'];
?>

<div class="order-row" data-order-id="<?= $id ?>"
     style="display: flex; align-items: stretch; gap: 10px; margin-bottom: 12px; padding: 0;">

    <?php include __DIR__ . '/../Shared/_item_card.php'; ?>

    <div class="glass-container"
         style="display: flex; flex-direction: column; justify-content: center;
                align-items: center; white-space: nowrap; padding: 0 20px; margin: 0; min-width: 100px;">
        <strong style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase;">Quantity</strong>
        <strong><?= (int)$itemCard['quantity'] ?></strong>
    </div>

    <div class="glass-container"
         style="display: flex; flex-direction: column; justify-content: center;
                align-items: center; white-space: nowrap; padding: 0 20px; margin: 0; min-width: 100px;">
        <div class="row-container">
            <label>Price</label>
            <h2 id="price-label-<?= $id ?>" class="money">
                $<?= number_format($totalPrice, 2) ?>
            </h2>
        </div>
        <div class="row-container">
            <label>Supplier</label>
            <select class="a-btn supplier-select"
                    style="background: rgba(0,0,0,0.2); width: 100%; text-align: left;"
                    onchange="handleSupplierChange(this, 'price-label-<?= $id ?>', <?= $totalPrice ?>)">

                <option value="0" data-multiplier="1">— select —</option>

                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= (int)$supplier['id'] ?>"
                            data-multiplier="<?= number_format((float)$supplier['price_multiplier'], 4, '.', '') ?>">
                        <?= htmlspecialchars($supplier['company_name']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>
    </div>

    <div class="glass-container"
         style="display: flex; flex-direction: column; justify-content: center;
                gap: 8px; padding: 12px 16px; margin: 0; min-width: 130px;">
        <button type="button" class="a-btn"
                onclick="handleSupply(<?= $id ?>, this.closest('.order-row'))">
            ✓ Supply
        </button>
        <button type="button" class="red-btn"
                onclick="handleCancel(<?= $id ?>, this.closest('.order-row'))">
            ✕ Cancel
        </button>
    </div>
</div>

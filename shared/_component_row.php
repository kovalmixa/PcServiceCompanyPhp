<?php
/**
 * _component_row.php  (Shared)
 * Equivalent of Views/Shared/_ComponentRow.cshtml
 *
 * Used inside PcConfiguration views to list bundled components.
 *
 * Expected: $itemCard (array):
 *   'id', 'name', 'type', 'brand', 'price' (float),
 *   'quantity' (int), 'image_path' (string|null)
 *
 * Optional: $isEdit (bool) – shows editable quantity input and remove button
 */

require_once __DIR__ . '/_helpers.php';

$isEdit       = $isEdit ?? false;
$id           = (int)  ($itemCard['id']       ?? 0);
$price        = (float)($itemCard['price']    ?? 0);
$quantity     = (int)  ($itemCard['quantity'] ?? 0);
$initialPrice = $price * $quantity;
?>

<div style="zoom: 0.75">
    <div class="row-container"
         style="align-items:stretch;gap:10px;margin-bottom:15px;padding:0;">

        <?php include __DIR__ . '/_item_card.php'; ?>

        <!-- Quantity cell -->
        <div class="glass-container"
             style="display:flex;flex-direction:column;justify-content:center;align-items:center;
                    white-space:nowrap;padding:0 20px;margin:0;min-width:100px;">
            <strong style="font-size:0.75rem;opacity:0.8;text-transform:uppercase;">Quantity</strong>

            <?php if ($isEdit): ?>
                <!-- hidden total-price tracker read by updateTotalPriceLabel() -->
                <input type="hidden"
                       id="total-price-<?= $id ?>"
                       total-price-id="price-label-<?= $id ?>"
                       value="<?= $initialPrice ?>">

                <input type="number"
                       id="quantity-input-<?= $id ?>"
                       name="quantity"
                       value="<?= $quantity ?>"
                       oninput="
                           let total = getTotal(<?= $price ?>, this.value);
                           if (total <= 0) document.getElementById('row-<?= $id ?>').remove();
                           updatePriceLabel('price-label-<?= $id ?>', total);
                           setValue('total-price-<?= $id ?>', total);
                           updateTotalPriceLabel();">
            <?php else: ?>
                <strong><?= $quantity ?></strong>
            <?php endif; ?>
        </div>

        <!-- Price cell -->
        <div class="glass-container"
             style="display:flex;align-items:center;justify-content:center;
                    width:150px;gap:10px;margin:0;padding:0 15px;">
            <strong id="price-label-<?= $id ?>" class="money">
                <?= money($initialPrice) ?>
            </strong>
        </div>

        <!-- Remove button (edit mode only) -->
        <?php if ($isEdit): ?>
            <button type="button" class="red-btn" style="font-size:300%;"
                    onclick="document.getElementById('row-<?= $id ?>').remove(); updateTotalPriceLabel()">
                &times;
            </button>
        <?php endif; ?>

    </div>
</div>

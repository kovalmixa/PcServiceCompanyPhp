<?php
require_once __DIR__ . '/_helpers.php';

if (!isset($componentData)) return;
$itemCardData = is_object($componentData) ? json_decode(json_encode($componentData), true) : $componentData;

$comp_id      = (int)  ($itemCardData['id']           ?? 0);
$name         =         $itemCardData['name']         ?? '';
$brand        =         $itemCardData['brand']        ?? '';
$type         =         $itemCardData['type']         ?? '';
$price        = (float)($itemCardData['price']        ?? 0);
$quality      =         $itemCardData['quality']      ?? 'N/A';
$quantity     = (int)  ($itemCardData['quantity']     ?? 1);

$initialPrice = $price * $quantity;
?>

<div id="row-<?= $id ?>" style="zoom: 0.75; width: 100%;">
    <div class="glass-container row-container" 
         style="display:flex; align-items:center; justify-content:space-between; gap:15px; margin-bottom:10px; padding:15px 20px;">
        
        <span style="flex:1; opacity:0.85;"><?= e($name) ?></span>

        <span style="flex:1; opacity:0.85;"><?= e($brand) ?></span>

        <span style="flex:1; opacity:0.85;"><?= e($type) ?></span>

        <span class="money" style="flex:1; font-weight:600;"><?= money($price) ?></span>

        <span style="flex:1; opacity:0.85;"><?= e($quality) ?></span>

        <div style="flex:1; display:flex; justify-content:center; align-items:center;">
            <?php if ($isEdit): ?>
                <input type="hidden" 
                       id="total-price-<?= $id ?>" 
                       total-price-id="price-label-<?= $id ?>" 
                       value="<?= $initialPrice ?>">

                <input type="number" 
                       id="quantity-input-<?= $id ?>" 
                       name="quantity" 
                       min="0"
                       value="<?= $quantity ?>"
                       style="width:60px; text-align:center; padding:4px;"
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

        <div style="flex:1.5; display:flex; align-items:center; justify-content:flex-end; gap:15px;">
            <strong id="price-label-<?= $id ?>" class="money" style="font-size:1.1rem;">
                <?= money($initialPrice) ?>
            </strong>
        </div>

    </div>
</div>
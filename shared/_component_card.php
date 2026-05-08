<?php
/**
 * _component_card.php
 * Equivalent of Views/Shared/_ComponentCard.cshtml
 *
 * Expected: $itemCard (array):
 *   'id', 'name', 'brand', 'type', 'price' (float),
 *   'quantity' (int), 'company_id' (int), 'image_path' (string|null)
 *
 * Optional: $isEdit (bool)
 */

require_once __DIR__ . '/_helpers.php';

$isEdit    = $isEdit ?? false;
$id        = (int)  ($itemCard['id']         ?? 0);
$name      =         $itemCard['name']        ?? '';
$brand     =         $itemCard['brand']       ?? '';
$type      =         $itemCard['type']        ?? '';
$price     = (float)($itemCard['price']      ?? 0);
$quantity  = (int)  ($itemCard['quantity']   ?? 0);
$companyId = (int)  ($itemCard['company_id'] ?? 0);
$img       = imgSrc($itemCard['image_path'] ?? null);
$role      = isCustomer() ? 'Customer' : 'Staff';
?>

<div class="row-container"
     style="align-items:stretch;gap:10px;margin-bottom:15px;padding:0;">

    <div class="glass-container"
         style="display:flex;align-items:center;gap:20px;flex:1;margin:0;padding:10px 20px;">
        <div style="display:flex;gap:20px;color:rgba(0,0,0,0.85);font-size:0.95rem;align-items:center;">
            <div class="center-container">

                <!-- Name row + optional admin delete button -->
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <h2 style="margin:0;font-size:1.25rem;text-align:center;flex:1;"><?= e($name) ?></h2>

                    <?php if (isInRole('Admin') && !$isEdit): ?>
                        <button type="button" class="red-btn"
                                onclick="if(confirm('Are you sure?'))
                                    sendActionRequest('/component/<?= $id ?>/delete','POST')"
                                style="width:38px;height:38px;padding:0;font-size:1.1rem;border-radius:8px;flex-shrink:0;">
                            &#x2715;
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Thumbnail -->
                <div style="flex:0 0 220px;display:flex;align-items:center;justify-content:center;
                            margin:10px 0;background:rgba(0,0,0,0.06);border-radius:12px;overflow:hidden;">
                    <?php if (!$isEdit): ?>
                        <a href="/component/<?= $id ?>">
                            <img src="<?= e($img) ?>" alt="<?= e($name) ?>"
                                 style="width:220px;height:220px;object-fit:contain;">
                        </a>
                    <?php else: ?>
                        <img draggable="false" src="<?= e($img) ?>" alt="<?= e($name) ?>"
                             style="width:220px;height:220px;object-fit:contain;">
                    <?php endif; ?>
                </div>

                <!-- Meta row -->
                <div class="row-container">
                    <strong><strong>Brand:</strong> <?= e($brand) ?></strong>
                    <strong><strong>Type:</strong> <?= e($type) ?></strong>
                    <?php if (!$isEdit): ?>
                        <strong id="quantity-label-<?= $id ?>">
                            <strong>Quantity:</strong> <?= $quantity ?>
                        </strong>
                    <?php endif; ?>
                </div>

                <!-- Price label -->
                <h2 id="price-label-<?= $id ?>" class="money" style="margin:0;">
                    <?= money($price) ?>
                </h2>

                <!-- Buy/order controls (only outside edit mode) -->
                <?php if (!$isEdit): ?>
                    <?php if (isInRole('Customer') && $quantity <= 0): ?>
                        <button disabled class="a-btn"
                                style="width:100%;opacity:0.5;cursor:not-allowed;">
                            Out of Stock
                        </button>
                    <?php else: ?>
                        <input id="quantity-input-<?= $id ?>"
                               oninput="updatePriceLabel('price-label-<?= $id ?>', getTotal(<?= $price ?>, this.value))"
                               type="number" value="1" min="1"
                               style="width:100%;padding:8px;border-radius:5px;
                                      background:rgba(0,0,0,0.05);border:1px solid #bbb;margin-bottom:10px;">

                        <?php if (isAuthenticated()): ?>
                            <button class="a-btn" style="width:100%;margin-top:auto;"
                                    onclick="handleOrder('<?= $id ?>',
                                        getValue('quantity-input-<?= $id ?>'),
                                        <?= $companyId ?>, true, '<?= e($role) ?>')">
                                <?= isCustomer() ? 'Add to Cart' : 'Order' ?>
                            </button>
                        <?php else: ?>
                            <a href="/auth/login" class="a-btn"
                               style="width:100%;margin-top:auto;text-align:center;
                                      text-decoration:none;display:block;">
                                Add to Cart
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

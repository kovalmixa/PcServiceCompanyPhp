<?php
require_once __DIR__ . '/_helpers.php';

$isEdit    = $isEdit ?? false;
$id        = (int)  ($itemCard['id']         ?? 0);
$name      =         $itemCard['name']        ?? '';
$brand     =         $itemCard['brand']       ?? '';
$type      =         $itemCard['type']        ?? '';
$price     = (float)($itemCard['price']      ?? 0);
$companyId = (int)  ($itemCard['company_id'] ?? 0);
$img       = imgSrc($itemCard['image_path']  ?? null);
?>

<div class="row-container"
     style="align-items:stretch;gap:10px;margin-bottom:15px;padding:0;">

    <div class="glass-container"
         style="display:flex;align-items:center;gap:20px;flex:1;margin:0;padding:10px 20px;">
        <div style="display:flex;gap:20px;color:rgba(0,0,0,0.85);font-size:0.95rem;align-items:center;">
            <div class="center-container">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <h2 style="margin:0;font-size:1.25rem;text-align:center;flex:1;">
                        <?= e($name) ?>
                    </h2>
                    <?php if (isAdminOrStaff()): ?>
                        <button type="button" class="red-btn"
                                onclick="if(confirm('Delete this configuration?'))
                                    fetch('<?= url('') ?>',{method:'POST',headers:{'X-CSRF-Token':document.querySelector('meta[name=csrf-token]').content}})"
                                style="width:38px;height:38px;padding:0;font-size:1.1rem;
                                       border-radius:8px;flex-shrink:0;">
                            &#x2715;
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ($isEdit): ?>
                    <img src="<?= e($img) ?>" alt="<?= e($name) ?>">
                <?php else: ?>
                    <a href="<?= url('pc_configuration', ['id' => $id]) ?>">
                        <img src="<?= e($img) ?>" alt="<?= e($name) ?>">
                    </a>
                <?php endif; ?>

                <div class="col-container">
                    <strong>Brand: <strong><?= e($brand) ?></strong></strong>
                    <strong>Type: <?= e($type) ?></strong>
                </div>

                <?php if (!$isEdit): ?>
                    <h2 class="money" style="margin:0;"><?= money($price) ?></h2>
                    <?php if (isAuthenticated()): ?>
                        <button <?= !isCustomer() ? 'disabled' : '' ?>
                                class="a-btn <?= isCustomer() ? '' : 'disabled' ?>"
                                onclick="handleOrder('<?= $id ?>', 1, <?= $companyId ?>, false)">
                            Add to Cart
                        </button>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="a-btn">Add to Cart</a>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

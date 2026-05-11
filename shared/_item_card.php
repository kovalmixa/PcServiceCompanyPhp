<?php
require_once __DIR__ . '/_helpers.php';

$isEdit = $isEdit ?? false;
$id     = (int)  ($itemCard['id']         ?? 0);
$name   =         $itemCard['name']        ?? '';
$type   =         $itemCard['type']        ?? '';
$brand  =         $itemCard['brand']       ?? '';
$price  = (float)($itemCard['price']      ?? 0);
$img    = imgSrc($itemCard['image_path'] ?? null);
?>

<div class="glass-container"
     style="display:flex;align-items:center;gap:20px;flex:1;margin:0;padding:10px 20px;">

    <h2 style="margin:0;width:150px;text-align:left;font-size:1.2rem;"><?= e($name) ?></h2>

    <?php if (!$isEdit): ?>
        <a href="/component/<?= $id ?>">
            <img src="<?= e($img) ?>" alt="<?= e($name) ?>"
                 style="width:220px;height:220px;object-fit:contain;">
        </a>
    <?php else: ?>
        <img draggable="false" src="<?= e($img) ?>" alt="<?= e($name) ?>"
             style="width:220px;height:220px;object-fit:contain;">
    <?php endif; ?>

    <div class="col-container"
         style="display:flex;gap:20px;color:rgba(0,0,0,0.85);font-size:0.95rem;align-items:center;">
        <label><strong>Type:</strong> <?= e($type) ?></label>
        <label><strong>Brand:</strong> <?= e($brand) ?></label>
        <label class="money"><strong>Price:</strong> <?= money($price) ?></label>
    </div>

</div>

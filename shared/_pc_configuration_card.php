<?php
require_once __DIR__ . '/_helpers.php';

if (!isset($configItem) || !($configItem instanceof PcConfiguration)) return;

$isEdit    = $isEdit ?? false;
$conf_id   = $configItem->id;
$name      = $configItem->name;
$brand     = $configItem->getBrand();
$price     = $configItem->getPrice();
$img       = imgSrc($configItem->image_path);

$components = $configItem->components ?? [];
$compBrands = [];
$qualitySum = 0;
$qualityCount = 0;

foreach ($components as $component) {
    $compArray = is_object($component) ? (array)$component : $component;
    
    if (!empty($compArray['brand'])) $compBrands[] = trim($compArray['brand']);
    if (isset($compArray['quality']) && $compArray['quality'] !== '') {
        $qualitySum += (float)$compArray['quality'];
        $qualityCount++;
    }
}

$uniqueBrands = array_unique($compBrands);
$brandsList = !empty($uniqueBrands) ? implode(', ', $uniqueBrands) : '—';
$avgQuality = $qualityCount > 0 ? round($qualitySum / $qualityCount, 1) : '—';
?>

<div class="row-container" style="align-items:stretch;gap:10px;margin-bottom:15px;padding:0;">
    <div class="glass-container" style="display:flex;align-items:center;gap:20px;flex:1;margin:0;padding:10px 20px;">
        <div style="display:flex;gap:20px;color:rgba(0,0,0,0.85);font-size:0.95rem;align-items:center;">
            <div class="center-container">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <h2 style="margin:0;font-size:1.25rem;text-align:center;flex:1;">
                        <?= e($name) ?>
                    </h2>
                    <?php if (isAdminOrStaff()): ?>
                        <button type="button" class="red-btn"
                                onclick="if(confirm('Delete this configuration?'))
                                    fetch('index.php?action=delete_pc_configuration&id=<?= $conf_id?>',{method:'POST',headers:{'X-CSRF-Token':document.querySelector('meta[name=csrf-token]').content}})"
                                style="width:38px;height:38px;padding:0;font-size:1.1rem;border-radius:8px;flex-shrink:0;">
                            &#x2715;
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ($isEdit): ?>
                    <img src="<?= e($img) ?>" alt="<?= e($name) ?>">
                <?php else: ?>
                    <a href="index.php?page=pc_configuration&id=<?= $conf_id?>">
                        <img src="<?= e($img) ?>" alt="<?= e($name) ?>">
                    </a>
                <?php endif; ?>

                <div class="col-container">
                    <strong>Brand: <span><?= e($brand) ?></span></strong>
                </div>

                <?php if (!$isEdit): ?>
                    <div class="col-container" style="margin-top:5px;">
                        <strong>Price: <span class="money" style="font-size:1.1rem;font-weight:700;"><?= money($price) ?></span></strong>
                    </div>

                    <div class="col-container">
                        <strong>Avg Quality: <span><?= $avgQuality ?><?= $qualityCount > 0 ? ' / 10' : '' ?></span></strong>
                    </div>

                    <div class="col-container" style="margin-bottom:10px;">
                        <strong>Components brands: <span style="font-weight:normal;opacity:0.8;"><?= e($brandsList) ?></span></strong>
                    </div>

                    <?php if (isAuthenticated()): ?>
                        <button <?= !isCustomer() ? 'disabled' : '' ?>
                                class="a-btn <?= isCustomer() ? '' : 'disabled' ?>"
                                onclick="handleOrder('<?= $conf_id?>', 1, false)">
                            Add to Cart
                        </button>
                    <?php else: ?>
                        <a href="index.php?page=login" class="a-btn">Add to Cart</a>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../shared/_helpers.php';

$config     = $config     ?? [];
$id         = (int)       ($config['id']         ?? 0);
$pc_name    =             $config['name']        ?? 'PC Configuration';
$pc_image   =             $config['image_path']  ?? null;
$components =             $config['components']  ?? [];

$total_price = 0;
$quality_sum = 0;
$quality_count = 0;

foreach ($components as $component) {
    $total_price += (float)($component['price'] ?? 0) * (int)($component['quantity'] ?? 1);
    if (isset($component['quality']) && $component['quality'] !== '') {
        $quality_sum += (float)$component['quality'];
        $quality_count++;
    }
}
$mean_quality = $quality_count > 0 ? round($quality_sum / $quality_count, 1) : '—';
ob_start();
?>
<?= csrfField() ?>

<div class="glass-container no-sticky"
     style="max-width:1100px;margin:40px auto;padding:30px;border-radius:20px;
            display:flex;flex-direction:column;gap:25px;">

    <div style="display:flex;justify-content:space-between;align-items:baseline;
                border-bottom:1px solid rgba(0,0,0,0.1);padding-bottom:15px;">
        <h1 style="margin:0;font-size:2.5rem;font-weight:700;"><?= e($pc_name) ?></h1>
    </div>

    <div style="display:grid;grid-template-columns:350px 1fr;gap:40px;align-items:start;">

        <div style="display:flex;flex-direction:column;gap:20px;">
            <div style="width:100%;height:250px;border-radius:15px;overflow:hidden;
                        background:rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.1);">
                <img src="<?= e(imgSrc($pc_image)) ?>" alt="<?= e($pc_name) ?>"
                     style="width:100%;height:100%;object-fit:contain;">
            </div>

            <div style="background:rgba(0,0,0,0.04);padding:20px;border-radius:15px;
                        text-align:center;border:1px solid rgba(0,0,0,0.08);">
                <strong style="display:block;font-size:0.9rem;opacity:0.5;
                               margin-bottom:5px;text-transform:uppercase;">Price per unit</strong>
                <strong class="money" style="display:block;font-size:2.2rem;font-weight:800;">
                    <?= money($total_price) ?>
                </strong>
            </div>
        </div>

        <div class="included-components-box" style="display:flex; flex-direction:column; gap:10px;">
            
            <div style="display:flex; justify-content:space-between; padding: 0 20px; margin-bottom: 5px;">
                <?php foreach (['Name','Brand','Type','Price ($)','Quality (0–10)','Qty','Total Price'] as $h): ?>
                    <span style="font-size:0.72rem; text-transform:uppercase; opacity:0.5; font-weight:600; flex: 1; text-align: left;"><?= $h ?></span>
                <?php endforeach; ?>
            </div>

            <div class="components-list" style="display:flex; flex-direction:column; gap:10px;">
                <?php if (!empty($components)): ?>
                    <?php foreach ($components as $componentData): ?>
                        <?php 
                        $isEdit = false; 
                        include __DIR__ . '/../shared/_component_row.php'; 
                        ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="opacity:0.5; padding:40px; text-align:center;">No components listed.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div style="padding-top:30px;border-top:1px solid rgba(0,0,0,0.1);
                display:flex;justify-content:space-between;align-items:flex-end;margin-top:15px;">
        
        <div style="display:flex;gap:40px;">
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:0.75rem;opacity:0.5;text-transform:uppercase;font-weight:bold;">Quantity</label>
                <input id="quantity-input"
                       oninput="updatePriceLabel('price-value', getTotal(<?= $total_price ?>, this.value))"
                       type="number" value="1" min="1"
                       style="background:rgba(0,0,0,0.05);border:1px solid #bbb;
                              border-radius:8px;padding:10px;width:80px;outline:none;">
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:0.75rem;opacity:0.5;text-transform:uppercase;font-weight:bold;">Total Sum</label>
                <h2 id="price-value" class="money"
                    style="margin:0;font-size:2.2rem;font-weight:800;line-height:1;">
                    <?= money($total_price) ?>
                </h2>
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:0.75rem;opacity:0.5;text-transform:uppercase;font-weight:bold;">Mean quality</label>
                <h2 id="price-value" class="money"
                    style="margin:0;font-size:2.2rem;font-weight:800;line-height:1;">
                    <?= $mean_quality ?>
                </h2>
            </div>
        </div>

        <div style="display:flex;gap:20px;align-items:center;">
            <?php if (isCustomer()): ?>
                <button class="a-btn" style="min-width:200px;padding:15px;"
                        onclick="handleOrder('<?= $id ?>', getValue('quantity-input'), false)">
                    Add to Cart
                </button>
            <?php else: ?>
                <a href="index.php?page=pc_configuration_edit&id=<?= $id ?>"
                   class="a-btn" style="padding:15px 35px;background:#333;color:#fff;font-weight:bold;
                          border-radius:10px;text-decoration:none;display:inline-block;">
                    Edit Config
                </a>
            <?php endif; ?>
            <a href="<?= url('pc_list') ?>"
               style="color:rgba(0,0,0,0.4);text-decoration:none;font-size:0.85rem;">
                ← Back
            </a>
        </div>

    </div>

</div>

<?php
$pageContent = ob_get_clean();

ob_start();
?>
<script type="module" src="<?= BASE_URL ?>js/price.js"></script>
<script type="module" src="<?= BASE_URL ?>js/server.js"></script>
<?php
$pageScripts = ob_get_clean();
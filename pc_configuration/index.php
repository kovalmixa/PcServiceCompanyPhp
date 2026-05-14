<?php
require_once __DIR__ . '/../shared/_helpers.php';

// ── Data (placeholder – load from DB by $_GET['id']) ─────────────────────────
$config      = $config ?? [];
$id          = (int)  ($config['id']               ?? 0);
$name        =         $config['name']              ?? '';
$type        =         $config['type']              ?? '';
$price       = (float)($config['price']            ?? 0);
$warrantyM   = (int)  ($config['warranty_months']  ?? 0);
$companyId   = (int)  ($config['company_id']       ?? 0);
$description =         $config['description']       ?? '';
$img         = imgSrc($config['image_path']        ?? null);
$bundles     =         $config['component_bundles'] ?? [];

// ── Render ────────────────────────────────────────────────────────────────────
ob_start();
?>
<?= csrfField() ?>

<div class="glass-container no-sticky"
     style="max-width:1100px;margin:40px auto;padding:30px;border-radius:20px;
            display:flex;flex-direction:column;gap:25px;">

    <div style="display:flex;justify-content:space-between;align-items:baseline;
                border-bottom:1px solid rgba(0,0,0,0.1);padding-bottom:15px;">
        <h1 style="margin:0;font-size:2.5rem;font-weight:700;"><?= e($name) ?></h1>
        <strong style="background:#333;padding:4px 12px;border-radius:6px;
                       font-size:0.8rem;font-weight:bold;text-transform:uppercase;color:#fff;">
            <?= e($type) ?>
        </strong>
    </div>

    <div style="display:grid;grid-template-columns:350px 1fr;gap:40px;">

        <div style="display:flex;flex-direction:column;gap:20px;">
            <div style="width:100%;height:250px;border-radius:15px;overflow:hidden;
                        background:rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.1);">
                <img src="<?= e($img) ?>" alt="<?= e($name) ?>"
                     style="width:100%;height:100%;object-fit:cover;">
            </div>

            <div style="background:rgba(0,0,0,0.04);padding:20px;border-radius:15px;
                        text-align:center;border:1px solid rgba(0,0,0,0.08);">
                <strong style="display:block;font-size:0.9rem;opacity:0.5;
                               margin-bottom:5px;text-transform:uppercase;">Price per unit</strong>
                <strong class="money" style="display:block;font-size:2.2rem;font-weight:800;">
                    <?= money($price) ?>
                </strong>
            </div>

            <strong>Warranty: <?= $warrantyM ?> months</strong>

            <div style="display:flex;flex-direction:column;gap:8px;">
                <strong style="font-size:0.8rem;opacity:0.5;text-transform:uppercase;">Description</strong>
                <div style="font-size:0.9rem;line-height:1.5;background:rgba(0,0,0,0.03);
                            padding:15px;border-radius:10px;border:1px solid rgba(0,0,0,0.06);">
                    <?= e($description ?: 'No description provided.') ?>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:15px;">
            <strong style="font-size:0.8rem;opacity:0.5;text-transform:uppercase;">
                Included Components
            </strong>
            <div style="overflow-y:auto;display:flex;flex-direction:column;gap:10px;">
                <?php foreach ($bundles as $itemCard): ?>
                    <div style="flex-shrink:0;width:100%;">
                        <?php include __DIR__ . '/../shared/_component_row.php'; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($bundles)): ?>
                    <p style="opacity:0.5;">No components listed.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div style="padding-top:30px;border-top:1px solid rgba(0,0,0,0.1);
                display:flex;justify-content:space-between;align-items:flex-end;">
        <div style="display:flex;gap:40px;">
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:0.75rem;opacity:0.5;text-transform:uppercase;font-weight:bold;">Quantity</label>
                <input id="quantity-input"
                       oninput="updatePriceLabel('price-value', getTotal(<?= $price ?>, this.value))"
                       type="number" value="1" min="1"
                       style="background:rgba(0,0,0,0.05);border:1px solid #bbb;
                              border-radius:8px;padding:10px;width:80px;outline:none;">
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:0.75rem;opacity:0.5;text-transform:uppercase;font-weight:bold;">Total Sum</label>
                <h2 id="price-value" class="money"
                    style="margin:0;font-size:2.2rem;font-weight:800;line-height:1;">
                    <?= money($price) ?>
                </h2>
            </div>
        </div>

        <div style="display:flex;gap:20px;align-items:center;">
            <?php if (isCustomer()): ?>
                <button class="a-btn" style="min-width:200px;padding:15px;"
                        onclick="handleOrder('<?= $id ?>', getValue('quantity-input'), <?= $companyId ?>, false)">
                    Add to Cart
                </button>
            <?php else: ?>
                <a href="<?= url('pc_configuration_edit', ['id' => $id]) ?>"
                   class="a-btn"
                   style="padding:15px 35px;background:#333;color:#fff;font-weight:bold;
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
<script type="module" src="<?= BASE_URL ?>js/data-base.js"></script>
<?php
$pageScripts = ob_get_clean();

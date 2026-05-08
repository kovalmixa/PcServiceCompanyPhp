<?php
/**
 * Component/index.php
 * Equivalent of Views/Component/Index.cshtml (Component.cshtml)
 *
 * Expected: $component (array):
 *   'id', 'name', 'brand', 'type', 'warranty_months', 'quality_level',
 *   'price' (float), 'company_id' (int),
 *   'description' (string), 'image_path' (string|null)
 */

require_once __DIR__ . '/../Shared/_helpers.php';

$pageTitle = 'PC Component';
$id            = (int)  ($component['id']              ?? 0);
$name          =         $component['name']             ?? '';
$brand         =         $component['brand']            ?? '';
$type          =         $component['type']             ?? '';
$warrantyM     = (int)  ($component['warranty_months'] ?? 0);
$quality       =         $component['quality_level']   ?? '';
$price         = (float)($component['price']           ?? 0);
$companyId     = (int)  ($component['company_id']      ?? 0);
$description   =         $component['description']     ?? '';
$img           = imgSrc($component['image_path'] ?? null);

ob_start();
?>

<?= csrfField() ?>

<div class="glass-container no-sticky" style="max-width:800px;margin:0 auto;">
    <h2>PC Component</h2>

    <div class="row-container" style="align-items:flex-start;gap:30px;">

        <!-- Left: text info -->
        <div class="center-container"
             style="flex:0 0 220px !important;text-align:left;margin:0;padding:0;">
            <div class="row-container">
                <label><strong>Name:</strong></label>
                <label><?= e($name) ?></label>
            </div>
            <div class="row-container">
                <label><strong>Brand:</strong></label>
                <label><?= e($brand) ?></label>
            </div>
            <div class="row-container">
                <label><strong>Type:</strong></label>
                <label><?= e($type) ?></label>
            </div>
            <div class="row-container">
                <label><strong>Warranty period:</strong></label>
                <label><?= $warrantyM ?></label>
            </div>
            <div class="row-container">
                <label><strong>Quality level</strong></label>
                <label><?= e($quality) ?></label>
            </div>
        </div>

        <!-- Right: image + price -->
        <div class="center-container" style="flex:1 !important;margin:0;padding:0;min-width:0;">
            <img src="<?= e($img) ?>" alt="<?= e($name) ?>">
            <div class="row-container">
                <label style="display:flex;flex-direction:column;align-items:center;
                              margin-top:10px;width:100%;">Price per unit</label>
                <label class="money"><?= money($price) ?></label>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="center-container" style="margin-top:20px;align-items:stretch;">
        <label style="text-align:left;margin-bottom:5px;">Component description</label>
        <div class="glass-container"
             style="padding:15px;border-radius:8px;min-height:100px;text-align:left;">
            <?= e($description) ?>
        </div>
    </div>

    <!-- Order row -->
    <div class="row-container" style="margin-top:30px;gap:15px;justify-content:flex-end;">
        <input id="quantity-input"
               oninput="updatePriceLabel('price-label', getTotal(<?= $price ?>, this.value))"
               type="number" value="1" min="1"
               style="width:100%;padding:8px;border-radius:5px;
                      background:rgba(0,0,0,0.05);border:1px solid #bbb;">

        <label id="price-label" class="money">$<?= number_format($price, 2) ?></label>

        <button class="a-btn" style="width:100%;margin-top:auto;"
                onclick="handleOrder('<?= $id ?>', getValue('quantity-input'),
                    <?= $companyId ?>, true, '<?= isCustomer() ? 'Customer' : 'Staff' ?>')">
            <?= isCustomer() ? 'Add to Cart' : 'Order' ?>
        </button>

        <?php if (!isCustomer()): ?>
            <a href="/component/<?= $id ?>/edit" class="a-btn">Edit</a>
        <?php endif; ?>
    </div>
</div>

<?php
$pageContent = ob_get_clean();

ob_start();
?>
<script type="module" src="/js/price.js"></script>
<?php
$pageScripts = ob_get_clean();

include __DIR__ . '/../Shared/_layout.php';

<?php
/**
 * PcConfiguration/edit.php
 * Equivalent of Views/PcConfiguration/Edit.cshtml (PcConfigurationEdit.cshtml)
 *
 * Expected: $config (array):
 *   'id' (int), 'name', 'type' (string), 'warranty_months' (int),
 *   'price_multiplier' (float), 'price' (float), 'description',
 *   'image_path' (string|null),
 *   'component_bundles' => [ ItemCard arrays with 'id','name','price','quantity',... ],
 *   'item_cards'        => [ ItemCard arrays (the browseable component pool) ]
 *
 * Optional: $errors (array), $pcTypes (array of strings)
 */

require_once __DIR__ . '/../Shared/_helpers.php';

$config     ??= [];
$errors     ??= [];
$pcTypes    ??= ['Desktop', 'Laptop', 'Workstation', 'Server', 'Mini'];

$isEdit     = !empty($config['id']) && (int)$config['id'] > 0;
$id         = (int)  ($config['id']               ?? 0);
$name       =         $config['name']              ?? '';
$type       =         $config['type']              ?? ($pcTypes[0] ?? '');
$warranty   = (int)  ($config['warranty_months']  ?? 0);
$multiplier = (float)($config['price_multiplier'] ?? 1.0);
$price      = (float)($config['price']            ?? 0);
$desc       =         $config['description']       ?? '';
$imagePath  =         $config['image_path']        ?? '';
$img        = imgSrc($imagePath ?: null);
$bundles    =         $config['component_bundles'] ?? [];
$itemCards  =         $config['item_cards']        ?? [];

$action     = $isEdit ? '/pc-configuration/update' : '/pc-configuration/create';
$pageTitle  = $isEdit ? 'Edit PC Configuration' : 'Add New PC Configuration';
$isEdit_card = true; // tell _component_card.php it's in edit mode

ob_start();
?>

<div class="glass-container no-sticky"
     style="width:1300px;margin:0 auto;padding:30px;border-radius:20px;
            display:flex;flex-direction:column;gap:25px;">

    <h2><?= $isEdit ? 'Edit PC Configuration' : 'Add New PC Configuration' ?></h2>

    <form action="<?= e($action) ?>" method="post" enctype="multipart/form-data">
        <?= csrfField() ?>

        <!-- JSON payload built by prepareData() before submit -->
        <input type="hidden" name="components_json" id="components-json">

        <?php if (!empty($errors['_general'])): ?>
            <div class="field-validation-error"><?= e($errors['_general']) ?></div>
        <?php endif; ?>

        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="image_path" value="<?= e($imagePath) ?>">

        <div class="row-container" style="align-items:stretch;margin-top:0;">

            <!-- ── Left column: main fields ──────────────────── -->
            <div class="center-container" style="flex:1;margin-top:0;">

                <div class="row-container mb-3">
                    <label class="row-label" for="cfg-name">Name</label>
                    <div style="flex:1;">
                        <input id="cfg-name" name="name" value="<?= e($name) ?>"
                               placeholder="e.g. Gaming PC">
                        <?php if (!empty($errors['name'])): ?>
                            <span class="field-validation-error"><?= e($errors['name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row-container mb-3">
                    <label class="row-label" for="cfg-type">Type</label>
                    <select id="cfg-type" name="type" class="a-btn"
                            style="background:rgba(0,0,0,0.2);width:100%;text-align:left;">
                        <?php foreach ($pcTypes as $pcType): ?>
                            <option value="<?= e($pcType) ?>"
                                    <?= ($type === $pcType) ? 'selected' : '' ?>>
                                <?= e($pcType) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row-container mb-3">
                    <label class="row-label" for="cfg-warranty">Warranty Months</label>
                    <div style="flex:1;">
                        <input id="cfg-warranty" name="warranty_months" type="number"
                               value="<?= $warranty ?>" placeholder="e.g. 12">
                        <?php if (!empty($errors['warranty_months'])): ?>
                            <span class="field-validation-error"><?= e($errors['warranty_months']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row-container mb-3">
                    <label class="row-label" for="price-multiplier">Price Multiplier</label>
                    <div style="flex:1;">
                        <input id="price-multiplier" name="price_multiplier" type="number"
                               step="0.01" value="<?= number_format($multiplier, 2, '.', '') ?>"
                               placeholder="e.g. 1.5"
                               onchange="updateTotalPriceLabel()">
                        <?php if (!empty($errors['price_multiplier'])): ?>
                            <span class="field-validation-error"><?= e($errors['price_multiplier']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Chosen-component rows (drag-drop target) -->
                <div id="row-component-container" class="glass-container no-sticky"
                     ondragover="onDragOver(event)"
                     ondrop="onDrop(event, async () => { await setComponentRow(getComponentId(event), 1); })"
                     style="height:500px;overflow-y:auto;margin-top:0;">
                    <?php foreach ($bundles as $itemCard): ?>
                        <?php
                        $bundleId = (int)($itemCard['id'] ?? 0);
                        $isEdit   = true; // show editable qty inside _component_row
                        ?>
                        <div id="row-<?= $bundleId ?>" data-component-id="<?= $bundleId ?>">
                            <?php include __DIR__ . '/../Shared/_component_row.php'; ?>
                        </div>
                        <?php $isEdit = false; // reset ?>
                    <?php endforeach; ?>
                </div>

                <div class="center-container mb-3">
                    <label for="cfg-desc">Description</label>
                    <textarea id="cfg-desc" name="description" rows="4"
                              style="align-content:stretch;"><?= e($desc) ?></textarea>
                </div>

            </div><!-- /left -->

            <!-- ── Right column: preview + component pool ─────── -->
            <div class="center-container"
                 style="display:flex;flex-direction:column;align-items:center;
                        justify-content:flex-start;gap:10px;margin-top:0;flex:0.4;">

                <label style="margin:0 0 10px 0;font-weight:bold;">PC Configuration Preview</label>

                <img id="pc-thumbnail-img" src="<?= e($img) ?>" alt="preview"
                     style="max-width:300px;height:auto;border-radius:8px;">

                <input id="file-input" type="file" name="upload_file" class="mt-2"
                       oninput="previewImage(this,'pc-thumbnail-img')">

                <h2 id="price-label" class="money" style="margin:10px 0;">
                    <?= money($price) ?>
                </h2>

                <!-- Browseable component pool (draggable cards) -->
                <div id="component-card-container" class="grid-container"
                     style="width:100%;height:350px;overflow-y:auto;">
                    <?php foreach ($itemCards as $i => $card): ?>
                        <?php
                        $cardId  = (int)($card['id'] ?? 0);
                        $itemCard = $card;
                        $isEdit   = true;
                        ?>
                        <div id="component-<?= $i ?>" draggable="true"
                             ondragstart="onDragStart(event)"
                             data-component-id="<?= $cardId ?>"
                             style="padding:15px;display:flex;flex-direction:column;zoom:0.8;">
                            <?php include __DIR__ . '/../Shared/_component_card.php'; ?>
                        </div>
                        <?php $isEdit = false; ?>
                    <?php endforeach; ?>
                </div>

                <div style="zoom:0.8;width:100%;">
                    <?php include __DIR__ . '/../Shared/_pagination.php'; ?>
                </div>

            </div><!-- /right -->

        </div><!-- /row-container -->

        <div class="row-container" style="gap:15px;justify-content:flex-start;margin-top:20px;">
            <button type="submit" onclick="prepareData(event)" class="a-btn">
                <?= $isEdit ? 'Save Changes' : 'Create Component' ?>
            </button>
            <a href="javascript:history.back()" class="a-btn"
               style="background:rgba(0,0,0,0.1);width:auto;">Cancel</a>
        </div>

    </form>
</div>

<?php
$pageContent = ob_get_clean();

ob_start();
?>
<script src="/js/image.js"></script>
<script type="module" src="/js/elements.js"></script>
<script src="/js/drag-drop.js"></script>
<script type="module">
    import { updatePriceLabel, getTotal } from '/js/price.js';

    const priceLabelId           = 'price-label';
    const priceMultiplierInputId = 'price-multiplier';
    const rowContainer           = document.getElementById('row-component-container');

    function prepareData(event) {
        const rows = rowContainer.querySelectorAll('[data-component-id]');
        if (rows.length === 0) console.warn('No components selected.');

        const components = Array.from(rows).map(row => {
            const id       = row.getAttribute('data-component-id');
            const qtyInput = row.querySelector('input[type="number"]')
                          || document.getElementById(`quantity-input-${id}`);
            return {
                componentId: parseInt(id),
                quantity:    parseInt(qtyInput?.value || '1')
            };
        }).filter(c => !isNaN(c.componentId) && c.quantity > 0);

        const jsonField = document.getElementById('components-json');
        if (jsonField) {
            jsonField.value = JSON.stringify(components);
        } else {
            console.error('components-json field not found!');
            event.preventDefault();
        }
    }

    function getComponentId(event) {
        const elementId = event.dataTransfer.getData('text/plain');
        const element   = document.getElementById(elementId);
        if (element) return element.getAttribute('data-component-id');
        const target = event.target.closest('[data-component-id]');
        return target ? target.getAttribute('data-component-id') : null;
    }

    async function setComponentRow(id, quantity) {
        if (id === null) return;
        const existing = rowContainer.querySelector(`[data-component-id="${id}"]`);
        if (!existing) {
            // Fetch rendered partial from PHP endpoint (placeholder route)
            const response = await fetch(`/pc-configuration/get-component-row?componentId=${id}`);
            if (response.ok) {
                const html    = await response.text();
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                const newRow  = tempDiv.firstElementChild;
                newRow.id     = `row-${id}`;
                newRow.setAttribute('data-component-id', id);
                rowContainer.appendChild(newRow);
            }
        } else {
            if (quantity < 0) {
                existing.remove();
            } else {
                const input = document.getElementById(`quantity-input-${id}`);
                if (input) {
                    input.value = (+input.value) + Number(quantity);
                    input.dispatchEvent(new Event('input'));
                }
            }
        }
        updateTotalPriceLabel();
    }

    function updateTotalPriceLabel() {
        const multiplier = parseFloat(document.getElementById(priceMultiplierInputId).value) || 1;
        const prices     = Array.from(rowContainer.querySelectorAll('[total-price-id]'))
                               .map(el => parseFloat(el.value) || 0);
        const total      = prices.reduce((acc, v) => acc + v, 0) * multiplier;
        updatePriceLabel(priceLabelId, total);
    }

    window.updateTotalPriceLabel = updateTotalPriceLabel;
    window.setComponentRow       = setComponentRow;
    window.getComponentId        = getComponentId;
    window.prepareData           = prepareData;
</script>
<?php
$pageScripts = ob_get_clean();

include __DIR__ . '/../Shared/_layout.php';

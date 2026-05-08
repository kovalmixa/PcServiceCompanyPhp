<?php
/**
 * Component/edit.php
 * Equivalent of Views/Component/Edit.cshtml (ComponentEdit.cshtml)
 *
 * Expected: $component (array) — empty/default for "create", populated for "edit":
 *   'id' (int), 'name', 'brand', 'type', 'price' (float),
 *   'sell_price_multiplier' (float), 'warranty_months' (int),
 *   'quality_level' (float), 'description', 'image_path' (string|null)
 *
 * Optional: $errors (array) — field-level validation messages from controller.
 */

require_once __DIR__ . '/../Shared/_helpers.php';

$component ??= [];
$errors    ??= [];

$isEdit    = !empty($component['id']) && (int)$component['id'] > 0;
$id        = (int)  ($component['id']                     ?? 0);
$name      =         $component['name']                    ?? '';
$brand     =         $component['brand']                   ?? '';
$type      =         $component['type']                    ?? 'CPU';
$price     = (float)($component['price']                  ?? 0);
$multiplier= (float)($component['sell_price_multiplier']  ?? 1.0);
$warranty  = (int)  ($component['warranty_months']        ?? 0);
$quality   = (float)($component['quality_level']          ?? 0);
$desc      =         $component['description']             ?? '';
$imagePath =         $component['image_path']              ?? '';
$img       = imgSrc($imagePath ?: null);

$action    = $isEdit ? '/component/update' : '/component/create';
$pageTitle = $isEdit ? 'Edit Component' : 'Add New Component';

$typeOptions = ['CPU', 'GPU', 'RAM', 'Motherboard'];

ob_start();
?>

<div class="glass-container">
    <h2><?= $isEdit ? 'Edit Component' : 'Add New Component' ?></h2>

    <form action="<?= e($action) ?>" method="post" enctype="multipart/form-data">
        <?= csrfField() ?>

        <?php if (!empty($errors['_general'])): ?>
            <div class="field-validation-error"><?= e($errors['_general']) ?></div>
        <?php endif; ?>

        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>
        <input type="hidden" name="image_path" value="<?= e($imagePath) ?>">

        <div class="row-container" style="align-items:stretch;">

            <!-- Left: fields -->
            <div class="center-container">

                <div class="row-container mb-3">
                    <label for="name">Name</label>
                    <div style="flex:1;">
                        <input id="name" name="name" value="<?= e($name) ?>"
                               placeholder="e.g. Core i9-13900K">
                        <?php if (!empty($errors['name'])): ?>
                            <span class="field-validation-error"><?= e($errors['name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row-container mb-3">
                    <label for="brand">Brand</label>
                    <div style="flex:1;">
                        <input id="brand" name="brand" value="<?= e($brand) ?>"
                               placeholder="Intel / AMD / ASUS">
                    </div>
                </div>

                <div class="row-container mb-3">
                    <label for="type">Type</label>
                    <div style="flex:1;">
                        <select id="type" name="type" class="a-btn"
                                style="background:rgba(0,0,0,0.2);width:100%;text-align:left;">
                            <?php foreach ($typeOptions as $opt): ?>
                                <option value="<?= e($opt) ?>"
                                    <?= ($type === $opt) ? 'selected' : '' ?>>
                                    <?= e($opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row-container mb-3">
                    <label for="price">Price</label>
                    <div style="flex:1;">
                        <input id="price" name="price" type="number" step="1"
                               value="<?= number_format($price, 0, '.', '') ?>">
                        <?php if (!empty($errors['price'])): ?>
                            <span class="field-validation-error"><?= e($errors['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <label for="sell_price_multiplier">Sell price multiplier</label>
                    <div style="flex:1;">
                        <input id="sell_price_multiplier" name="sell_price_multiplier"
                               type="number" step="0.01"
                               value="<?= number_format($multiplier, 2, '.', '') ?>">
                        <?php if (!empty($errors['sell_price_multiplier'])): ?>
                            <span class="field-validation-error"><?= e($errors['sell_price_multiplier']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row-container mb-3" style="align-items:center;gap:20px;">
                    <!-- Warranty -->
                    <div style="display:flex;align-items:center;gap:10px;flex:1;">
                        <label for="warranty_months"
                               style="flex:none;width:auto;margin:0;">Warranty (Months)</label>
                        <div style="flex:1;">
                            <input id="warranty_months" name="warranty_months"
                                   type="number" value="<?= $warranty ?>" style="width:100%;">
                            <?php if (!empty($errors['warranty_months'])): ?>
                                <span class="field-validation-error"><?= e($errors['warranty_months']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Quality slider -->
                    <div style="display:flex;align-items:center;gap:10px;flex:1;">
                        <label style="flex:none;width:auto;margin:0;white-space:nowrap;">
                            Quality: <span id="quality-val"><?= $quality ?></span>
                        </label>
                        <div style="flex:1;display:flex;flex-direction:column;">
                            <input oninput="updateSliderValue('quality-input-hidden','quality-val',this)"
                                   type="range" min="0" max="10" step="0.1"
                                   value="<?= $quality ?>"
                                   style="width:100%;margin:0;cursor:pointer;">
                            <input type="hidden" name="quality_level"
                                   id="quality-input-hidden" value="<?= $quality ?>">
                            <?php if (!empty($errors['quality_level'])): ?>
                                <span class="field-validation-error"><?= e($errors['quality_level']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div><!-- /center-container -->

            <!-- Right: image preview -->
            <div class="col-container center-container">
                <label style="margin:10px;">Component Preview</label>
                <img id="thumbnail-img" src="<?= e($img) ?>" alt="preview">
                <input id="file-input" type="file" name="upload_file" class="mt-2"
                       oninput="previewImage(this, 'thumbnail-img')">
            </div>

        </div><!-- /row-container -->

        <!-- Description -->
        <div class="center-container mb-3">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= e($desc) ?></textarea>
        </div>

        <!-- Actions -->
        <div class="row-container" style="gap:15px;justify-content:flex-start;">
            <button type="submit" class="a-btn">
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
<script type="module" src="/js/elements.js"></script>
<script src="/js/image.js"></script>
<?php
$pageScripts = ob_get_clean();

include __DIR__ . '/../Shared/_layout.php';

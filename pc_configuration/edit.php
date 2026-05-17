<?php
require_once __DIR__ . '/../shared/_helpers.php';

$config    = $config    ?? [];
$errors    = $errors    ?? [];
$isEdit    = !empty($config['id']) && (int)$config['id'] > 0;
$id        = (int)  ($config['id']         ?? 0);
$name      =         $config['name']        ?? '';
$imagePath =         $config['image_path']  ?? '';
$img       = imgSrc($imagePath ?: null);
$components = $config['components'] ?? [];
$pageTitle = $isEdit ? 'Edit PC Configuration' : 'New PC Configuration';
ob_start();
?>
<link rel="stylesheet" href="<?= BASE_URL ?>css/grid.css">

<div class="glass-container no-sticky"
     style="max-width:860px;margin:40px auto;padding:36px;border-radius:20px;
            display:flex;flex-direction:column;gap:28px;">

    <h2 style="margin:0;"><?= $isEdit ? 'Edit PC Configuration' : 'New PC Configuration' ?></h2>

    <?php if (!empty($errors['_general'])): ?>
        <div class="field-validation-error"><?= e($errors['_general']) ?></div>
    <?php endif; ?>

    <div style="display:flex;flex-direction:column;gap:6px;">
        <label for="cfg-name" style="font-weight:600;">Configuration Name</label>
        <input id="cfg-name" name="name" value="<?= e($name) ?>"
               placeholder="e.g. Gaming Beast Pro">
        <?php if (!empty($errors['name'])): ?>
            <span class="field-validation-error"><?= e($errors['name']) ?></span>
        <?php endif; ?>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;">
        <label style="font-weight:600;">Cover Image</label>
        <div style="display:flex;align-items:flex-start;gap:20px;">
            <img id="cfg-thumbnail"
                 src="<?= e($img) ?>"
                 alt="preview"
                 style="width:140px;height:100px;object-fit:cover;border-radius:10px;
                        border:1px solid rgba(0,0,0,0.12);background:rgba(0,0,0,0.04);">
            <div style="display:flex;flex-direction:column;gap:8px;">
                <input id="file-input" type="file" accept="image/*"
                       onchange="previewImageFromFile(this, 'cfg-thumbnail')">
                <span style="font-size:0.8rem;opacity:0.5;">PNG, JPG, WEBP — max 4 MB</span>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:32px;padding:16px 20px;background:rgba(0,0,0,0.04);
                border-radius:12px;border:1px solid rgba(0,0,0,0.08);">
        <div style="display:flex;flex-direction:column;gap:2px;">
            <span style="font-size:0.72rem;text-transform:uppercase;opacity:0.5;font-weight:600;">
                Total Price
            </span>
            <span id="summary-price" class="money" style="font-size:1.7rem;font-weight:800;">
                $0.00
            </span>
        </div>
        <div style="display:flex;flex-direction:column;gap:2px;">
            <span style="font-size:0.72rem;text-transform:uppercase;opacity:0.5;font-weight:600;">
                Avg Quality
            </span>
            <span id="summary-quality" style="font-size:1.7rem;font-weight:800;">
                —
            </span>
        </div>
        <div style="display:flex;flex-direction:column;gap:2px;">
            <span style="font-size:0.72rem;text-transform:uppercase;opacity:0.5;font-weight:600;">
                Components
            </span>
            <span id="summary-count" style="font-size:1.7rem;font-weight:800;">0</span>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <label style="font-weight:600;">Components</label>
            <button type="button" class="a-btn"
                    style="width:auto;padding:8px 18px;margin:0;"
                    onclick="addComponentRow()">
                + Add Component
            </button>
        </div>

        <div id="component-header"
             style="display:none;grid-template-columns:2fr 1.5fr 1.2fr 1fr 1fr 1fr 36px;
                    gap:8px;padding:0 6px;">
            <?php foreach (['Name','Brand','Type','Price ($)','Quality (0–10)','Qty',''] as $h): ?>
                <span style="font-size:0.72rem;text-transform:uppercase;
                             opacity:0.5;font-weight:600;"><?= $h ?></span>
            <?php endforeach; ?>
        </div>

        <div id="component-list" style="display:flex;flex-direction:column;gap:8px;"></div>
    </div>

    <div style="display:flex;gap:12px;padding-top:10px;border-top:1px solid rgba(0,0,0,0.08);">
        <button type="button" class="a-btn"
                style="width:auto;padding:12px 32px;margin:0;"
                onclick="submitConfiguration(<?= $id ?>, <?= $isEdit ? 'true' : 'false' ?>)">
            <?= $isEdit ? 'Save Changes' : 'Create Configuration' ?>
        </button>
        <a href="<?= url('pc_list') ?>" class="a-btn"
           style="background:rgba(0,0,0,0.08);color:#111;width:auto;
                  padding:12px 24px;margin:0;text-decoration:none;display:flex;
                  align-items:center;">
            Cancel
        </a>
    </div>

</div>

<script>
const PREFILLED_BUNDLES = <?= json_encode(array_values($components)) ?>;
</script>
<?php
$pageContent = ob_get_clean();

ob_start();
?>
<script src="<?= BASE_URL ?>js/image.js"></script>
<script>
function previewImageFromFile(input, imgId) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById(imgId).src = e.target.result;
    reader.readAsDataURL(file);
}

let rowIndex = 0;

function buildRow(data = {}) {
    const i   = rowIndex++;
    const def = { name:'', brand:'', type:'', price:'', quality:'', quantity:1, ...data };

    const row = document.createElement('div');
    row.className   = 'component-row glass-container';
    row.dataset.row = i;
    row.style.cssText = `
        display:grid;
        grid-template-columns:2fr 1.5fr 1.2fr 1fr 1fr 1fr 36px;
        gap:8px;align-items:center;padding:10px 14px;margin:0;
    `;

    const field = (name, placeholder, value, type='text', extra='') =>
        `<input data-field="${name}" type="${type}" placeholder="${placeholder}"
                value="${value}" ${extra}
                oninput="recalcSummary()"
                style="margin:0;padding:8px 10px;font-size:0.9rem;">`;

    row.innerHTML = `
        ${field('name',     'Name',   def.name)}
        ${field('brand',    'Brand',  def.brand)}
        ${field('type',     'Type',   def.type)}
        ${field('price',    '0.00',   def.price,   'number', 'min="0" step="0.01"')}
        ${field('quality',  '0–10',   def.quality, 'number', 'min="0" max="10" step="0.1"')}
        ${field('quantity', '1',      def.quantity,'number', 'min="1" step="1"')}
        <button type="button"
                onclick="removeRow(this)"
                style="width:36px;height:36px;padding:0;margin:0;font-size:1.1rem;
                       display:flex;align-items:center;justify-content:center;
                       background:rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.2);
                       color:#111;border-radius:8px;cursor:pointer;flex-shrink:0;">
            ×
        </button>
    `;
    return row;
}

function addComponentRow(data = {}) {
    const list = document.getElementById('component-list');
    list.appendChild(buildRow(data));
    document.getElementById('component-header').style.display = 'grid';
    recalcSummary();
}

function removeRow(btn) {
    btn.closest('.component-row').remove();
    const list = document.getElementById('component-list');
    if (!list.querySelector('.component-row'))
        document.getElementById('component-header').style.display = 'none';
    recalcSummary();
}

function recalcSummary() {
    const rows     = document.querySelectorAll('.component-row');
    let totalPrice = 0;
    let qualitySum = 0;
    let qualityN   = 0;

    rows.forEach(row => {
        const price    = parseFloat(row.querySelector('[data-field="price"]').value)    || 0;
        const qty      = parseInt(  row.querySelector('[data-field="quantity"]').value) || 1;
        const quality  = parseFloat(row.querySelector('[data-field="quality"]').value);

        totalPrice += price * qty;
        if (!isNaN(quality)) { qualitySum += quality; qualityN++; }
    });

    document.getElementById('summary-price').textContent =
        '$' + totalPrice.toFixed(2);
    document.getElementById('summary-quality').textContent =
        qualityN > 0 ? (qualitySum / qualityN).toFixed(1) : '—';
    document.getElementById('summary-count').textContent = rows.length;
}

function collectComponents() {
    return Array.from(document.querySelectorAll('.component-row')).map(row => ({
        name:     row.querySelector('[data-field="name"]').value.trim(),
        brand:    row.querySelector('[data-field="brand"]').value.trim(),
        type:     row.querySelector('[data-field="type"]').value.trim(),
        price:    parseFloat(row.querySelector('[data-field="price"]').value)    || 0,
        quality:  parseFloat(row.querySelector('[data-field="quality"]').value)  || 0,
        quantity: parseInt(  row.querySelector('[data-field="quantity"]').value) || 1,
    }));
}

async function submitConfiguration(id, isEdit) {
    const name       = document.getElementById('cfg-name').value.trim();
    const fileInput  = document.getElementById('file-input');
    const imageFile  = fileInput.files[0] ?? null;
    const components = collectComponents();
    
    console.log('submitConfiguration called', {
        id,
        isEdit,
        name,
        imageFile,
        components,
    });
    if (!name) {
        alert('Please provide a configuration name.');
        return; 
    }
    if (!components || components.length === 0) {
        alert('Add at least one component to the configuration.');
        return;
    }

    const fd = new FormData();
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) fd.append('csrf_token', csrfMeta.content);
    fd.append('id', id > 0 ? id : '');
    fd.append('name', name);
    fd.append('components', JSON.stringify(components));
    if (imageFile) fd.append('image', imageFile);
    
   try {
        const res = await fetch('index.php?action=save_pc_configuration', { method: 'POST', body: fd });
        const responseText = await res.text();

        try {
            const data = JSON.parse(responseText);
            if (data.success) {
                const finalId = data.id || id;
                window.location.href = `index.php?page=pc_configuration&id=${finalId}`;
            } else {
                alert('Server error: ' + data.error);
            }
        } catch (jsonError) {
            console.error('The server did not return JSON. Server response:', responseText);
            alert('Critical server error. Details in the browser console.');
        }
    } catch (fetchError) {
        console.error('Network error:', fetchError);
        alert('Failed to connect to the server.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (PREFILLED_BUNDLES && PREFILLED_BUNDLES.length) {
        PREFILLED_BUNDLES.forEach(b => addComponentRow(b));
    } else {
        addComponentRow();
    }
});
</script>
<?php
$pageScripts = ob_get_clean();

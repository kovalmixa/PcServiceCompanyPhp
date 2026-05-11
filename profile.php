<?php
require_once __DIR__ . '/../shared/_helpers.php';

$pageTitle = 'Profile';
$fields = [
    ['name' => 'email',            'label' => 'Email',            'type' => 'email'],
    ['name' => 'name',             'label' => 'Name',             'type' => 'text'],
    ['name' => 'phone',            'label' => 'Phone',            'type' => 'tel'],
];

ob_start();
?>

<div class="glass-container">
    <div class="container">
        <h3>Registration</h3>

        <form action="/auth/register" method="post">
            <?= csrfField() ?>

            <?php foreach ($fields as $field): ?>
                <div class="row-container mb-3">
                    <label class="row-label" for="<?= e($field['name']) ?>">
                        <?= e($field['label']) ?>
                    </label>
                    <div style="flex:1;">
                        <input id="<?= e($field['name']) ?>"
                               name="<?= e($field['name']) ?>"
                               type="<?= e($field['type']) ?>"
                               value="<?= $field['type'] === 'password' ? '' : e($old[$field['name']] ?? '') ?>">
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="a-btn">Save changes</button>
        </form>

    </div>
</div>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../Shared/_layout.php';

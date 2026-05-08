<?php
/**
 * Auth/register.php
 * Equivalent of Views/Auth/Register.cshtml (Register.cshtml)
 *
 * Optional: $errors (array) – field-level validation messages
 *           $old (array)    – repopulate form on validation failure
 */

require_once __DIR__ . '/../Shared/_helpers.php';

$pageTitle = 'Register';
$errors    = $errors ?? [];
$old       = $old    ?? [];

$fields = [
    ['name' => 'email',            'label' => 'Email',            'type' => 'email'],
    ['name' => 'password',         'label' => 'Password',         'type' => 'password'],
    ['name' => 'confirm_password', 'label' => 'Confirm Password', 'type' => 'password'],
    ['name' => 'name',             'label' => 'Name',             'type' => 'text'],
    ['name' => 'surname',          'label' => 'Surname',          'type' => 'text'],
    ['name' => 'phone',            'label' => 'Phone',            'type' => 'tel'],
    ['name' => 'address',          'label' => 'Address',          'type' => 'text'],
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
                        <?php if (!empty($errors[$field['name']])): ?>
                            <span class="field-validation-error">
                                <?= e($errors[$field['name']]) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="a-btn">Register</button>
        </form>

    </div>
</div>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../Shared/_layout.php';

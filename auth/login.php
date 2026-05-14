<?php
$pageTitle = 'Login';
$errors    = $errors ?? [];
$old       = $old    ?? [];
?>
<form action="<?= BASE_URL ?>index.php?page=login" method="post">
    <?= csrfField() ?>
    <div class="glass-container">
        <div class="col_container">
            <?php if (!empty($errors['_general'])): ?>
                <div class="field-validation-error" style="margin-bottom:10px;">
                    <?= e($errors['_general']) ?>
                </div>
            <?php endif; ?>

            <div class="row-container mb-3">
                <label class="row-label" for="email">Email</label>
                <div style="flex:1;">
                    <input id="email" name="email" type="email"
                           value="<?= e($old['email'] ?? '') ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="field-validation-error"><?= e($errors['email']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row-container mb-3">
                <label class="row-label" for="password">Password</label>
                <div style="flex:1;">
                    <input id="password" name="password" type="password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <span class="field-validation-error"><?= e($errors['password']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <button class="a-btn" type="submit">Login</button>
        </div>
    </div>
</form>
<?php
$pageContent = ob_get_clean();
ob_start();
?>
<?php
require_once __DIR__ . '/../shared/_helpers.php';
require_once __DIR__ . '/../shared/_data_base.php';

$pageTitle = 'Profile';
$sql  = "SELECT id, name, email, phone, role FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$fields = [
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'dbValue' => $user['email'], 'isEdit' => true],
    ['name' => 'name',  'label' => 'Name',  'type' => 'text', 'dbValue' => $user['name'], 'isEdit' => true],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'dbValue' => $user['phone'], 'isEdit' => true],
    ['name' => 'role', 'label' => 'Role', 'type' => 'text', 'dbValue' => $user['role'], 'isEdit' => false]
];

ob_start();
?>
<div style="max-width:700px;margin:60px auto;">
    <div class="glass-container">
        <h3>Profile</h3>
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
                            value="<?= e($field['dbValue'] ?? '') ?>"
                            <?= !$field['isEdit'] ? 'readonly' : '' ?>>
                </div>
            </div>
        <?php endforeach; ?>
        <button class="a-btn" onclick="sendActionRequest(
            '<?= BASE_URL ?>profile/update_profile.php', 
            'POST', { 
            id: <?= $user['id'] ?>, 
            name: getValue('name'), 
            phone: getValue('phone'), 
            email: getValue('email')})">
            Save Changes
        </button>
    </div>
</div>
<script type="module" src="<?= BASE_URL ?>js/elements.js"></script>
<script type="module" src="<?= BASE_URL ?>js/server.js"></script>
<?php
$pageContent = ob_get_clean();

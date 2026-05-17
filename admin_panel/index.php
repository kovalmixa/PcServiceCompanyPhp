<?php
require_once __DIR__ . '/../shared/_data_base.php';
require_once __DIR__ . '/../shared/_helpers.php';

$sql  = "SELECT id, name, email, role FROM users WHERE id != ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

ob_start();
?>
<link rel="stylesheet" href="<?= BASE_URL ?>css/table.css">
<h2 class="center-container">User Management</h2>
<div style="display: flex; flex-direction: column; gap: 50px; max-width: 1000px; margin: 20px auto;">
    <div class="glass-container no-sticky">
        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><div style="font-weight:bold;"><?= e($user['name']) ?></div></td>
                            <td><?= e($user['email']) ?></td>
                            <td style="text-align:right;">
                                    <select onchange="sendActionRequest(
                                            '<?= BASE_URL ?>admin_panel/update_role.php', 
                                            'POST', { 
                                            id: <?= $user['id'] ?>, 
                                            role: this.value 
                                        })"
                                        class="a-btn"
                                        style="background:rgba(0,0,0,0.2);width:100%;text-align:left;">
                                    <?php foreach (UserRole::cases() as $role): ?>
                                        <option value="<?= $role->value ?>"
                                                <?= (int)$user['role'] === $role->value ? 'selected' : '' ?>>
                                            <?= e($role->label()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="glass-container" >
        <?php
            include __DIR__ . '/../shared/_mail.php';
            renderMailWidget([
                'subject' => 'PC Configuration web-site users',
                'template' => 'users',
                'data' => ['users' => $users]
            ]);
        ?>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
ob_start();
?>
<script type="module" src="<?= BASE_URL ?>js/server.js"></script>
<?php
$pageScripts = ob_get_clean();
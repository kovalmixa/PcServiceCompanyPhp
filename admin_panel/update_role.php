<?php
require_once __DIR__ . '/../shared/_helpers.php';
require_once __DIR__ . '/../shared/_data_base.php';

prepareContent();
if (!isAuthenticated() || !isInRole(UserRole::Admin)) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}
$data = getDataFromJSON();

$id = isset($data['id']) ? (int)$data['id'] : null;
$roleValue = isset($data['role']) ? (int)$data['role'] : null;

if ($id && $roleValue) {
    try {
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$roleValue, $id]);
        if ($success) echo json_encode(['success' => true]);
        else echo json_encode(['success' => false, 'error' => 'Unable to update data']);
    } catch (\PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Data base error' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Incorrect input data: ID=' . $id . ' Role=' . $roleValue]);
}
exit;
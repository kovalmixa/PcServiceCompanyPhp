<?php
require_once __DIR__ . '/../shared/_helpers.php';
require_once __DIR__ . '/../shared/_data_base.php';

prepareContent();
if (!isAuthenticated()) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}
$data = getDataFromJSON();

$id = isset($data['id']) ? (int)$data['id'] : null;
$name = isset($data['name']) ? (string)$data['name'] : null;
$email = isset($data['email']) ? (string)$data['email'] : null;
$phone = isset($data['phone']) ? (int)$data['phone'] : null;

if ($id && $name && $email && $phone) {
    try {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$name, $email, $phone, $id]);
        if ($success) echo json_encode(['success' => true]);
        else echo json_encode(['success' => false, 'error' => 'Unable to update data']);
    } catch (\PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Data base error' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Incorrect input data: ID=' . $id . 
    ' Name=' . $name . ' Email=' . $email . ' Phone=' . $phone]);
}
exit;
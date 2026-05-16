<?php
require_once __DIR__ . '/../shared/_helpers.php';
require_once __DIR__ . '/../shared/_data_base.php';

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');
if (!isAuthenticated()) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

$id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
$name = isset($_POST['name']) ? trim((string)$_POST['name']) : null;
$componentsRaw = $_POST['components'] ?? '';
$components = json_decode($componentsRaw, true);
if (empty($name) || !is_array($components)){
    echo json_encode(['success' => false, 'error' => 'Invalid data: Name or Components missing.']);
    exit;
}
$uploadDir = __DIR__ . '/../uploads/';
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imagePath = handleImageUpload($_FILES['image'], $uploadDir);
    if ($imagePath === null) {
        echo json_encode(['success' => false, 'error' => 'Failed to save uploaded image']);
        exit;
    }
}

try {
    $pdo->beginTransaction();
    if (trySavePcConfiguration($pdo, $name, $imagePath, $id)) {
        $configurationId = $id ?? (int)$pdo->lastInsertId();
        saveComponents($pdo, $configurationId, $components);
        $pdo->commit();
        echo json_encode(['success' => true]);
    } else {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Configuration with this name might already exist']);
    }
} catch (\Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
exit;

function handleImageUpload(array $file, string $uploadDir): ?string {
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array(strtolower($extension), $allowed)) return null;

    $fileName = uniqid('img_', true) . '.' . $extension;
    $destination = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $destination)) return '/uploads/' . $fileName;
    return null;
}

function trySavePcConfiguration(\PDO $pdo, string $name, ?string $imagePath, ?int $id = null): bool {
    if ($id !== null) {
        $sql = "SELECT COUNT(*) FROM pc_configurations WHERE name = ? AND id != ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $id]);
    } else {
        $sql = "SELECT COUNT(*) FROM pc_configurations WHERE name = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name]);
    }

    if ((int)$stmt->fetchColumn() > 0) return false;
    if ($id === null) {
        $sql = "INSERT INTO pc_configurations (name, image_path) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$name, $imagePath]);
    } else {
        $sql = "UPDATE pc_configurations SET name = ?, image_path = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$name, $imagePath, $id]);
    }
}

function saveComponents(\PDO $pdo, int $configurationId, array $components): void {
    $deleteSql = "DELETE FROM pc_components WHERE pc_configuration_id = ?";
    $pdo->prepare($deleteSql)->execute([$configurationId]);

    if (empty($components)) return;

    $rows = [];
    $values = [];

    foreach ($components as $component) {
        $rows[] = '(?, ?, ?, ?, ?, ?)';
        $values[] = $configurationId;
        $values[] = isset($component['name']) ? (string)$component['name'] : 'Unknown';
        $values[] = isset($component['quantity']) ? (int)$component['quantity'] : 1;
        $values[] = isset($component['brand']) ? (string)$component['brand'] : null;
        $values[] = isset($component['type']) ? (string)$component['type'] : null;
        $values[] = isset($component['price']) ? (float)$component['price'] : 0.0;
    }

    $sql = "INSERT INTO pc_components (pc_configuration_id, name, quantity, brand, type, price) VALUES " . implode(', ', $rows);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
}
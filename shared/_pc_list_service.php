<?php
class PcConfigurationService {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getPaginatedList(int $currentPage, bool $isAdminOrStaff): array {
        $perPage = $isAdminOrStaff ? 29 : 30;

        $countSql = "SELECT COUNT(*) FROM pc_configurations";
        $totalItems = $this->pdo->query($countSql)->fetchColumn();
        
        $totalPages = (int)ceil($totalItems / $perPage); 
        if ($totalPages < 1) $totalPages = 1;
        
        $offset = ($currentPage - 1) * $perPage;
        if ($offset < 0) $offset = 0;

        $sql = "SELECT * FROM pc_configurations ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $configurations = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'items' => $configurations,
            'pagination' => [
                'current' => $currentPage,
                'total' => $totalPages,
                'has_pages' => $totalPages > 1
            ]
        ];
    }

    public function getPcConfiguration(int $id): array {
        $config = [];
        if ($id > 0) {
            $sql = "SELECT 
                c.id AS cfg_id, c.name AS cfg_name, c.image_path AS cfg_image,
                comp.id AS comp_id, comp.name AS comp_name, comp.brand, 
                comp.type, comp.price, comp.quality, comp.quantity
            FROM pc_configurations c
            LEFT JOIN pc_components comp ON comp.pc_configuration_id = c.id
            WHERE c.id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $config = [];
            if (!empty($rows)) {
                $config = [
                    'id' => $rows[0]['cfg_id'],
                    'name' => $rows[0]['cfg_name'],
                    'image_path' => $rows[0]['cfg_image'],
                    'components' => []
                ];
                foreach ($rows as $row) {
                    if ($row['comp_id'] !== null) {
                        $config['components'][] = [
                            'id' => $row['comp_id'],
                            'name' => $row['comp_name'],
                            'brand' => $row['brand'],
                            'type' => $row['type'],
                            'price' => $row['price'],
                            'quality' => $row['quality'],
                            'quantity' => $row['quantity']
                        ];
                    }
                }
            }
        }
        return $config;
    } 
}
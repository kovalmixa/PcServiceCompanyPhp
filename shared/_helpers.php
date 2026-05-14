<?php
define('BASE_URL', 'http://localhost/website/');

if (session_status() === PHP_SESSION_NONE) session_start();

enum UserRole: int {
    case Admin = 1;
    case Staff = 2;
    case User  = 3;
    public function label(): string {
        return match($this) {
            self::Admin => 'Admin',
            self::Staff => 'Staff',
            self::User  => 'User',
        };
    }
}

function isAuthenticated(): bool {
    return !empty($_SESSION['user_id']);
}

function isInRole(UserRole $role): bool {
    $sessionRole = $_SESSION['user_role'] ?? null;
    return (int)$sessionRole === $role->value;
}

function isAdminOrStaff(): bool {
    return isInRole(UserRole::Admin) || isInRole(UserRole::Staff);
}

function isCustomer(): bool {
    return isInRole(UserRole::User) || !isAuthenticated();
}

function money(float $amount): string {
    return '$' . number_format($amount, 2);
}

function imgSrc(?string $path, string $placeholder = 'images/place-holder.jpg'): string {
    if (empty($path)) return BASE_URL . $placeholder;
    if (str_starts_with($path, 'http') || str_starts_with($path, '/')) return $path;
    return BASE_URL . ltrim($path, '/');
}

function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function csrfField(): string {
    $token = $_SESSION['csrf_token'] ?? '';
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

function url(string $page, array $params = []): string {
    $q = array_merge(['page' => $page], $params);
    return BASE_URL . 'index.php?' . http_build_query($q);
}

function prepareContent() {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
}

function getDataFromJSON() {
    $json = file_get_contents('php://input');
    return json_decode($json, true);
}
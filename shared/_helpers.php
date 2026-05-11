<?php
require_once __DIR__ . '/_auth.php';

define('BASE_URL', 'http://localhost/website/');

function isAuthenticated(): bool {
    return !empty($_SESSION['user_id']);
}

function isInRole(UserRole $role): bool {
    return (($_SESSION['role'] ?? '') === $role);
}

function money(float $amount): string {
    return '$' . number_format($amount, 2);
}

function imgSrc(?string $path, string $placeholder = '/images/place-holder.jpg'): string {
    if (empty($path)) return $placeholder;
    return str_starts_with($path, '/') ? $path : '/' . $path;
}

function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function csrfField(): string {
    $token = $_SESSION['csrf_token'] ?? '';
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

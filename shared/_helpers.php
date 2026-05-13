<?php
define('BASE_URL', 'http://localhost/website/');

enum UserRole: int 
{
    case Admin = 1;
    case Staff = 2;
    case User = 3;
}

function isAuthenticated(): bool {
    return !empty($_SESSION['user_id']);
}

function isInRole(UserRole $role): bool {
    $sessionRole = $_SESSION['user_role'] ?? '';
    return $sessionRole === $role->value;
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

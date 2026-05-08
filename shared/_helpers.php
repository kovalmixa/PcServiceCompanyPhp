<?php
/**
 * _helpers.php – auth & formatting helpers
 * Include at the top of any view that needs role checks.
 */

function isAuthenticated(): bool {
    return !empty($_SESSION['user_id']);
}

function isInRole(string $role): bool {
    return (($_SESSION['role'] ?? '') === $role);
}

function isCustomer(): bool {
    return isInRole('Customer') || !isAuthenticated();
}

function isAdminOrStaff(): bool {
    return isInRole('Admin') || isInRole('Staff');
}

/** Format a float as a dollar amount, e.g. "$1,234.56" */
function money(float $amount): string {
    return '$' . number_format($amount, 2);
}

/** Return safe image src: prepend "/" when needed, fall back to placeholder. */
function imgSrc(?string $path, string $placeholder = '/images/place-holder.jpg'): string {
    if (empty($path)) return $placeholder;
    return str_starts_with($path, '/') ? $path : '/' . $path;
}

/** HTML-escape a string. */
function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/** CSRF token hidden input. */
function csrfField(): string {
    $token = $_SESSION['csrf_token'] ?? '';
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

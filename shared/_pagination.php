<?php
/**
 * Pagination partial – _pagination.php
 * Equivalent of Views/Shared/_Pagination.cshtml (partial used in Index)
 *
 * Expected variables (set by controller/router):
 *   $currentPage  (int)  – current page number, 1-based
 *   $totalPages   (int)  – total number of pages
 *   $baseUrl      (string) – URL prefix for page links, e.g. '/staff/component-order?page='
 */

$currentPage = (int)($currentPage ?? 1);
$totalPages  = (int)($totalPages  ?? 1);
$baseUrl     = $baseUrl ?? '?page=';

$hasPrev = $currentPage > 1;
$hasNext = $currentPage < $totalPages;
?>

<div class="pagination-footer">
    <div class="glass-container">
        <div class="pagination-layout">

            <a href="<?= htmlspecialchars($baseUrl . ($currentPage - 1)) ?>"
               class="a-btn auto-width <?= !$hasPrev ? 'disabled' : '' ?>"
               <?= !$hasPrev ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
                &larr; Prev
            </a>

            <span>Page <?= $currentPage ?> of <?= $totalPages ?></span>

            <a href="<?= htmlspecialchars($baseUrl . ($currentPage + 1)) ?>"
               class="a-btn auto-width <?= !$hasNext ? 'disabled' : '' ?>"
               <?= !$hasNext ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
                Next &rarr;
            </a>

        </div>
    </div>
</div>

<?php

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

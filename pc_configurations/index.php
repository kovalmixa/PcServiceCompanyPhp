<?php
/**
 * PcConfigurations/index.php
 * Equivalent of Views/PcConfigurations/Index.cshtml (PcConfigurations.cshtml)
 *
 * Expected: $configurations (array of ItemCard arrays)
 * Optional: $totalPages (int) – if > 1 the pagination partial is shown
 */

require_once __DIR__ . '/../Shared/_helpers.php';

$pageTitle      = 'PC Configurations';
$configurations ??= [];
$totalPages     = (int)($totalPages ?? 1);

ob_start();
?>

<link rel="stylesheet" href="/css/grid.css">

<h2>PC Configurations</h2>

<div style="max-width:1480px;margin:0 auto;padding:0 20px;">
    <div class="grid-container">

        <?php if (isInRole('Admin') || isInRole('Staff')): ?>
            <a href="/pc-configuration/edit"
               style="display:block;text-decoration:none;height:100%;">
                <div class="glass-container"
                     style="height:440px;display:flex;align-items:center;justify-content:center;
                            border-radius:16px;cursor:pointer;transition:all 0.2s ease;">
                    <span style="font-size:5.5rem;font-weight:200;
                                 color:rgba(0,0,0,0.45);line-height:1;">+</span>
                </div>
            </a>
        <?php endif; ?>

        <?php foreach ($configurations as $itemCard): ?>
            <?php include __DIR__ . '/../Shared/_pc_configuration_card.php'; ?>
        <?php endforeach; ?>

    </div>

    <?php if ($totalPages > 1): ?>
        <?php include __DIR__ . '/../Shared/_pagination.php'; ?>
    <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();

ob_start();
?>
<script type="module" src="/js/price.js"></script>
<script type="module" src="/js/elements.js"></script>
<script type="module" src="/js/data-base.js"></script>
<?php
$pageScripts = ob_get_clean();

include __DIR__ . '/../Shared/_layout.php';

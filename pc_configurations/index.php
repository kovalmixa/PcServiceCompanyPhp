<?php
require_once __DIR__ . '/../shared/_helpers.php';

/** 
 * @var array|object[] $configurations
 * @var array $paginationData
*/

ob_start();
?>
<link rel="stylesheet" href="<?= BASE_URL ?>css/grid.css">

<h2 style="padding: 0 20px;">PC Configurations</h2>

<div style="max-width:1480px;margin:0 auto;padding:0 20px;">
    <div class="grid-container">
        <?php if (isAdminOrStaff()): ?>
            <a href="index.php?page=pc_configuration_edit" style="display:block;text-decoration:none;height:100%;">
                <div class="glass-container"
                     style="height:440px;display:flex;align-items:center;justify-content:center;
                            border-radius:16px;cursor:pointer;transition:all 0.2s ease;">
                    <span style="font-size:5.5rem;font-weight:200;color:rgba(0,0,0,0.45);line-height:1;">+</span>
                </div>
            </a>
        <?php endif; ?>

        <?php 
        foreach ($configurations as $configItem): 
            include __DIR__ . '/../shared/_pc_configuration_card.php'; 
        
        endforeach; 
        ?>

        <?php if (empty($configurations)): ?>
            <p style="opacity:0.5;padding:40px;">No PC configurations found.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($paginationData) && $paginationData['has_pages']): ?>
        <?php 
        $totalPages = $paginationData['total'];
        $currentPage = $paginationData['current'];
        include __DIR__ . '/../shared/_pagination.php'; 
        ?>
    <?php endif; ?>
</div>
<?php
$pageContent = ob_get_clean();

ob_start();
?>
<script type="module" src="<?= BASE_URL ?>js/server.js"></script>
<?php
$pageScripts = ob_get_clean();
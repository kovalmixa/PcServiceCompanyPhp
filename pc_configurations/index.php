<?php
/**
 * @var PcConfiguration[] $configurations
 * @var array             $paginationData   { has_pages, total, current }
 */

if (session_status() === PHP_SESSION_NONE) session_start();
$searchQuery = $_SESSION['search_query_string'] ?? '';
ob_start();
?>
<link rel="stylesheet" href="<?= BASE_URL ?>css/grid.css">

<h2 style="padding:0 20px;">PC Configurations</h2>

<form action="index.php" method="GET" class="row-container" style="gap:10px;margin-bottom:20px;align-items:center;">
    <input type="hidden" name="action" value="search_configurations">
    <input name="q" 
           value="<?= e($searchQuery) ?>" 
           placeholder="Search by name, brand, type — or use quality:>5 price:<1500 …"
           style="flex:1; padding: 10px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.15);" />
    <button type="submit" class="a-btn" style="width:auto;margin:0;padding:10px 25px;">
        Search
    </button>
    
    <?php if ($searchQuery !== ''): ?>
        <a href="index.php?action=search_configurations&q=" class="a-btn"
           style="width:auto;margin:0;padding:10px 18px; text-decoration:none; background:rgba(0,0,0,0.08);color:#111;">
            ✕ Clear
        </a>
    <?php endif; ?>
</form>

<div style="max-width:1480px;margin:0 auto;padding:0 20px;">
    
    <?php if ($searchQuery !== ''): ?>
        <p style="opacity:0.6;margin:0 0 12px;font-size:0.9rem;">
            Showing search results for: "<?= e($searchQuery) ?>"
        </p>
    <?php endif; ?>

    <div id="grid-container" class="grid-container">
        <?php if (isAdminOrStaff()): ?>
            <a id="add-new-card" href="index.php?page=pc_configuration_edit" style="display:block;text-decoration:none;height:100%;">
                <div class="glass-container" style="height:440px;display:flex;align-items:center;justify-content:center;border-radius:16px;cursor:pointer;">
                    <span style="font-size:5.5rem;font-weight:200;color:rgba(0,0,0,0.45);line-height:1;">+</span>
                </div>
            </a>
        <?php endif; ?>

        <?php foreach ($configurations as $configItem): ?>
            <?php include __DIR__ . '/../shared/_pc_configuration_card.php'; ?>
        <?php endforeach; ?>

        <?php if (empty($configurations)): ?>
            <p style="opacity:0.5;padding:40px;grid-column:1/-1;">No PC configurations found.</p>
        <?php endif; ?>
    </div>

    <div id="normal-pagination">
        <?php if (isset($paginationData) && $paginationData['has_pages']): ?>
            <?php
            $totalPages  = $paginationData['total'];
            $currentPage = $paginationData['current'];
            $baseUrl     = 'index.php?page=pc_list&p=';
            include __DIR__ . '/../shared/_pagination.php';
            ?>
        <?php endif; ?>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
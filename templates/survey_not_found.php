<?php
/**
 * @var \App\View\View $view
 * @var string|null $shortCode
 */
?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="glass-card empty-state">
            <i class="fas fa-magnifying-glass fa-3x mb-3" style="color:#dc2626; opacity:0.6;"></i>
            <h1 class="page-title">Poll not found</h1>
            <?php if ($shortCode !== null): ?>
                <p class="lead-muted mb-4">
                    No poll exists for the code
                    <span class="badge-code mx-1"><?= $view->escape($shortCode) ?></span>.
                </p>
            <?php else: ?>
                <p class="lead-muted mb-4">No poll code was provided.</p>
            <?php endif; ?>
            <a class="btn btn-primary" href="index.php">
                <i class="fas fa-arrow-left me-1"></i> Back to overview
            </a>
        </div>
    </div>
</div>

<?php
/**
 * @var \App\View\View $view
 * @var \App\Survey\Survey[] $surveys
 * @var bool $isAuthenticated
 */
?>
<section class="hero">
    <div class="container">
        <h1>Active polls</h1>
        <p>Cast your vote and watch the results update in real time.</p>
    </div>
</section>

<?php if ($surveys === []): ?>
    <div class="glass-card empty-state">
        <i class="fas fa-square-poll-vertical fa-3x mb-3" style="color:#6366f1; opacity:0.5;"></i>
        <p class="mb-3">There are no polls yet.</p>
        <?php if ($isAuthenticated): ?>
            <a class="btn btn-primary" href="admin.php">
                <i class="fas fa-plus me-1"></i> Create the first poll
            </a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="stack">
        <?php foreach ($surveys as $survey): ?>
            <a class="survey-row" href="survey.php?c=<?= $view->escape($survey->shortCode) ?>">
                <div>
                    <div class="question"><?= $view->escape($survey->question) ?></div>
                    <div class="meta-row">
                        <span><i class="fas fa-users me-1"></i><?= $survey->totalVotes() ?> vote<?= $survey->totalVotes() === 1 ? '' : 's' ?></span>
                        <?php if ($survey->isClosed()): ?>
                            <span class="badge-status badge-closed"><i class="fas fa-lock me-1"></i>Closed</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-code"><?= $view->escape($survey->shortCode) ?></span>
                    <i class="fas fa-arrow-right" style="color:rgba(255,255,255,0.4);"></i>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

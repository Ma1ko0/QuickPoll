<?php
/**
 * @var \App\View\View $view
 * @var \App\Survey\Survey $survey
 * @var string $csrf
 * @var bool $hasVoted
 * @var string|null $errorMessage
 * @var string $shareUrl
 */

$totalVotes = $survey->totalVotes();
?>
<?php if ($errorMessage !== null): ?>
    <div class="alert alert-danger">
        <i class="fas fa-circle-exclamation me-2"></i><?= $view->escape($errorMessage) ?>
    </div>
<?php endif; ?>

<div class="glass-card">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
        <h2 style="margin:0; font-size:1.4rem; flex:1;">
            <?= $view->escape($survey->question) ?>
        </h2>
        <span class="badge-code"><?= $view->escape($survey->shortCode) ?></span>
    </div>
    <p class="lead-muted mb-4">
        <i class="fas fa-clock me-1"></i><?= $view->escape($survey->createdAt) ?>
        &nbsp;&middot;&nbsp;
        <i class="fas fa-users me-1"></i><?= $totalVotes ?> vote<?= $totalVotes === 1 ? '' : 's' ?>
        <?php if ($survey->expiresAt !== null): ?>
            &nbsp;&middot;&nbsp;
            <?php if ($survey->isClosed()): ?>
                <i class="fas fa-lock me-1"></i>Closed <?= $view->escape($survey->expiresAt) ?>
            <?php else: ?>
                <i class="fas fa-hourglass-half me-1"></i>Closes <?= $view->escape($survey->expiresAt) ?>
            <?php endif; ?>
        <?php endif; ?>
    </p>

    <?php if ($hasVoted || $survey->isClosed()): ?>
        <p class="lead-muted mb-3">
            <?php if ($survey->isClosed()): ?>
                <i class="fas fa-lock me-1" style="color:#6366f1;"></i>
                This poll is closed. Final results:
            <?php else: ?>
                <i class="fas fa-circle-check me-1" style="color:#6366f1;"></i>
                You have voted. Here are the current results:
            <?php endif; ?>
        </p>
        <?php foreach ($survey->options as $option):
            $percentage = $option->percentageOf($totalVotes);
        ?>
            <div class="vote-result">
                <div class="vote-fill" style="width: <?= max($percentage, 0) ?>%;"></div>
                <div class="vote-result-row">
                    <strong><?= $view->escape($option->label) ?></strong>
                    <span class="vote-result-meta">
                        <?= $option->voteCount ?> &middot; <?= $percentage ?>%
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <form method="post" action="survey.php?c=<?= $view->escape($survey->shortCode) ?>">
            <input type="hidden" name="csrf" value="<?= $view->escape($csrf) ?>">
            <?php foreach ($survey->options as $option): ?>
                <label class="vote-card">
                    <input type="radio" name="option_id" value="<?= $option->id ?>" required>
                    <span><?= $view->escape($option->label) ?></span>
                </label>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary mt-3 w-100">
                <i class="fas fa-paper-plane me-1"></i> Submit vote
            </button>
        </form>
    <?php endif; ?>
</div>

<div class="glass-card">
    <h2><i class="fas fa-share-nodes me-2" style="color:#8b5cf6;"></i>Share this poll</h2>
    <p class="lead-muted mb-3">Send this link to let others vote directly:</p>
    <div class="share-row">
        <i class="fas fa-link" style="color:rgba(255,255,255,0.4);"></i>
        <input type="text" readonly value="<?= $view->escape($shareUrl) ?>" onclick="this.select();">
        <button type="button" class="btn btn-primary btn-sm" data-copy="<?= $view->escape($shareUrl) ?>">
            <i class="fas fa-copy me-1"></i>Copy
        </button>
    </div>
</div>

<?php
/**
 * @var \App\View\View $view
 * @var \App\Http\UrlBuilder $urlBuilder
 * @var \App\Survey\Survey[] $surveys
 * @var string $csrf
 * @var string|null $flashMessage
 * @var string|null $errorMessage
 * @var string $submittedQuestion
 * @var array $submittedOptions
 * @var string $submittedExpiry
 */
?>
<h1 class="page-title">
    <i class="fas fa-gauge-high me-2" style="color:#6366f1;"></i>Admin dashboard
</h1>

<?php if ($flashMessage !== null): ?>
    <div class="alert alert-success">
        <i class="fas fa-circle-check me-2"></i><?= $view->escape($flashMessage) ?>
    </div>
<?php endif; ?>

<?php if ($errorMessage !== null): ?>
    <div class="alert alert-danger">
        <i class="fas fa-circle-exclamation me-2"></i><?= $view->escape($errorMessage) ?>
    </div>
<?php endif; ?>

<div class="glass-card">
    <h2><i class="fas fa-plus-circle me-2" style="color:#6366f1;"></i>Create a new poll</h2>
    <form method="post" action="admin.php?action=create">
        <input type="hidden" name="csrf" value="<?= $view->escape($csrf) ?>">

        <div class="mb-3">
            <label class="form-label" for="question">Question</label>
            <input type="text" id="question" name="question" class="form-control" required maxlength="200"
                   placeholder="e.g. What is your favourite colour?"
                   value="<?= $view->escape($submittedQuestion) ?>">
        </div>

        <label class="form-label">Options</label>
        <div id="options-container" class="stack mb-3">
            <?php
            $defaultOptions = $submittedOptions === [] ? ['', '', ''] : $submittedOptions;
            foreach ($defaultOptions as $index => $optionValue):
            ?>
                <input type="text"
                       name="options[]"
                       class="form-control"
                       maxlength="100"
                       <?= $index < 2 ? 'required' : '' ?>
                       placeholder="Option <?= $index + 1 ?><?= $index >= 2 ? ' (optional)' : '' ?>"
                       value="<?= $view->escape((string) $optionValue) ?>">
            <?php endforeach; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="expires_at">
                Closing date <span class="lead-muted">(optional &mdash; leave empty to keep it open)</span>
            </label>
            <input type="datetime-local" id="expires_at" name="expires_at" class="form-control"
                   value="<?= $view->escape($submittedExpiry) ?>">
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-light" onclick="addOptionField()">
                <i class="fas fa-plus me-1"></i> Add option
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i> Create poll
            </button>
        </div>
    </form>
</div>

<h2 style="font-size:1.15rem; font-weight:600; margin-top:2.5rem; margin-bottom:1rem;">
    <i class="fas fa-list me-2" style="color:#8b5cf6;"></i>All polls
    <span class="lead-muted" style="font-weight:400;">(<?= count($surveys) ?>)</span>
</h2>

<?php if ($surveys === []): ?>
    <div class="glass-card empty-state">No polls yet.</div>
<?php else: ?>
    <?php foreach ($surveys as $survey):
        $shareUrl = $urlBuilder->surveyUrl($survey->shortCode);
        $totalVotes = $survey->totalVotes();
    ?>
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div style="flex:1; min-width:0;">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                        <strong style="font-size:1.05rem;"><?= $view->escape($survey->question) ?></strong>
                        <span class="badge-code"><?= $view->escape($survey->shortCode) ?></span>
                    </div>
                    <div class="meta-row">
                        <span><i class="fas fa-users me-1"></i><?= $totalVotes ?> vote<?= $totalVotes === 1 ? '' : 's' ?></span>
                        <span><i class="fas fa-clock me-1"></i><?= $view->escape($survey->createdAt) ?></span>
                        <?php if ($survey->isClosed()): ?>
                            <span class="badge-status badge-closed"><i class="fas fa-lock me-1"></i>Closed</span>
                        <?php elseif ($survey->expiresAt !== null): ?>
                            <span class="badge-status badge-open"><i class="fas fa-hourglass-half me-1"></i>Closes <?= $view->escape($survey->expiresAt) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn btn-outline-light btn-sm" href="<?= $view->escape($shareUrl) ?>" target="_blank">
                        <i class="fas fa-arrow-up-right-from-square me-1"></i>Open
                    </a>
                    <form method="post" action="admin.php?action=delete"
                          onsubmit="return confirm('Delete this survey?');"
                          style="display:inline;">
                        <input type="hidden" name="csrf" value="<?= $view->escape($csrf) ?>">
                        <input type="hidden" name="survey_id" value="<?= $survey->id ?>">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-3">
                <?php if ($totalVotes === 0): ?>
                    <p class="lead-muted mb-0">
                        <i class="fas fa-chart-simple me-1"></i>No votes yet.
                    </p>
                <?php else: ?>
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
                <?php endif; ?>
            </div>

            <div class="share-row mt-3">
                <i class="fas fa-link" style="color:rgba(255,255,255,0.4);"></i>
                <input type="text" readonly value="<?= $view->escape($shareUrl) ?>" onclick="this.select();">
                <button type="button" class="btn btn-primary btn-sm" data-copy="<?= $view->escape($shareUrl) ?>">
                    <i class="fas fa-copy me-1"></i>Copy
                </button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function addOptionField() {
        const container = document.getElementById('options-container');
        const index = container.children.length + 1;
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'options[]';
        input.className = 'form-control';
        input.maxLength = 100;
        input.placeholder = 'Option ' + index + ' (optional)';
        container.appendChild(input);
    }
</script>

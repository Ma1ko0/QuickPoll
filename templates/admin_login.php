<?php
/**
 * @var \App\View\View $view
 * @var string $csrf
 * @var string|null $errorMessage
 */
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="glass-card" style="padding: 2.25rem;">
            <div class="text-center mb-4">
                <i class="fas fa-shield-alt fa-2x mb-3" style="color:#6366f1;"></i>
                <h1 class="page-title mb-1">Admin sign-in</h1>
                <p class="lead-muted">Sign in to manage your polls.</p>
            </div>

            <?php if ($errorMessage !== null): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-circle-exclamation me-2"></i><?= $view->escape($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="admin.php?action=login">
                <input type="hidden" name="csrf" value="<?= $view->escape($csrf) ?>">
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-right-to-bracket me-1"></i> Sign in
                </button>
            </form>

        </div>
    </div>
</div>

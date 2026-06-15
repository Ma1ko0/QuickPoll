<?php
/**
 * @var \App\View\View $view
 * @var string $title
 * @var string $content
 * @var string $activeNav
 * @var bool   $isAuthenticated
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $view->escape($title) ?> &middot; QuickPoll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #8b5cf6;
            --danger: #e11d48;
            --ring: rgba(99, 102, 241, 0.25);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        a { color: var(--primary); text-decoration: none; }
        a:hover { color: var(--primary-dark); }

        .navbar {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }
        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text);
            font-size: 1.25rem;
        }
        .navbar-brand .brand-mark {
            background: linear-gradient(90deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .navbar .nav-link { color: var(--muted); font-weight: 500; }
        .navbar .nav-link:hover,
        .navbar .nav-link.active { color: var(--text); }

        main.page {
            flex: 1;
            padding-top: 100px;
            padding-bottom: 4rem;
        }

        h1.page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }
        .lead-muted {
            color: var(--muted);
            font-size: 1rem;
        }

        .glass-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            color: var(--text);
            padding: 1.75rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.05);
        }
        .glass-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            letter-spacing: -0.01em;
        }

        .hero {
            padding: 70px 1.5rem 60px;
            text-align: center;
            position: relative;
            margin-bottom: 2rem;
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: #fff;
            box-shadow: 0 18px 40px rgba(99, 102, 241, 0.28);
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
        }
        .hero p {
            font-size: 1.15rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 560px;
            margin: 0 auto;
        }

        .btn {
            font-weight: 600;
            border-radius: 12px;
            padding: 0.6rem 1.4rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border: none;
            color: #fff;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
        }
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background: linear-gradient(90deg, var(--primary-dark), var(--accent)) !important;
            border: none;
            color: #fff;
            box-shadow: 0 10px 22px rgba(99, 102, 241, 0.38);
        }
        .btn-outline-light {
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
        }
        .btn-outline-light:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--text);
        }
        .btn-danger {
            background: var(--danger);
            border: none;
        }
        .btn-danger:hover { background: #be123c; }
        .btn-sm { padding: 0.4rem 0.95rem; font-size: 0.85rem; }

        .form-control,
        .form-control:focus {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 12px;
            padding: 0.65rem 0.95rem;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px var(--ring);
            border-color: var(--primary);
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
            margin-bottom: 0.4rem;
        }

        .badge-code {
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-dark);
            border: 1px solid rgba(99, 102, 241, 0.25);
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            font-family: ui-monospace, "SF Mono", Menlo, monospace;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.04em;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .badge-open {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-dark);
        }
        .badge-closed {
            background: rgba(100, 116, 139, 0.14);
            color: #475569;
        }

        .vote-card {
            position: relative;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s ease;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .vote-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.12);
        }
        .vote-card input[type=radio] {
            accent-color: var(--primary);
            width: 1.1rem;
            height: 1.1rem;
            flex-shrink: 0;
        }
        .vote-card span { position: relative; z-index: 2; flex: 1; }
        .vote-fill {
            position: absolute;
            top: 0; left: 0;
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            opacity: 0.16;
            transition: width 0.8s ease;
            z-index: 1;
        }
        .vote-result {
            position: relative;
            padding: 0.85rem 1.1rem;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 0.6rem;
        }
        .vote-result-row {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 0.5rem;
        }
        .vote-result-row strong { font-weight: 600; }
        .vote-result-meta {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .share-row {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            background: #f1f5f9;
            padding: 0.6rem 0.85rem;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        .share-row input {
            flex: 1;
            border: 0;
            background: transparent;
            padding: 0;
            margin: 0;
            font-family: ui-monospace, "SF Mono", Menlo, monospace;
            font-size: 0.85rem;
            color: #334155;
        }
        .share-row input:focus { outline: none; }

        .survey-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.1rem 1.35rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 0.6rem;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
        }
        .survey-row:hover {
            border-color: var(--primary);
            color: inherit;
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.07);
        }
        .survey-row .question { font-weight: 600; flex: 1; }
        .survey-row .meta { color: var(--muted); font-size: 0.85rem; }

        .alert {
            border-radius: 12px;
            border: 1px solid;
        }
        .alert-success {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }
        .alert-danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--muted);
        }

        footer.site-footer {
            background: transparent;
            padding: 40px 0;
            text-align: center;
            color: var(--muted);
            font-size: 0.9rem;
            border-top: 1px solid var(--border);
        }
        footer.site-footer a { color: var(--muted); }
        footer.site-footer a:hover { color: var(--primary); }

        .actions { display: flex; gap: 0.4rem; flex-shrink: 0; }
        .meta-row {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 0.4rem;
            color: var(--muted);
            font-size: 0.85rem;
        }
        .stack > * + * { margin-top: 0.6rem; }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2.2rem; }
            .hero { padding: 56px 1rem 40px; }
            h1.page-title { font-size: 1.5rem; }
            .glass-card { padding: 1.25rem; }
            .survey-row { flex-direction: column; align-items: flex-start; }
            main.page { padding-top: 88px; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-square-poll-vertical me-2" style="color:var(--primary);"></i>
                <span class="brand-mark">Quick</span>Poll
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $activeNav === 'home' ? 'active' : '' ?>" href="index.php">
                            <i class="fas fa-chart-pie me-1"></i> Overview
                        </a>
                    </li>
                    <?php if ($isAuthenticated): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeNav === 'admin' ? 'active' : '' ?>" href="admin.php">
                                <i class="fas fa-gauge-high me-1"></i> Admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?action=logout">
                                <i class="fas fa-right-from-bracket me-1"></i> Sign out
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeNav === 'admin' ? 'active' : '' ?>" href="admin.php">
                                <i class="fas fa-lock me-1"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="page">
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="mb-2">&copy; <?= date('Y') ?> QuickPoll &middot; Open-source survey app.</p>
            <div class="mt-3">
                <a href="https://github.com/Ma1ko0/quickpoll" class="me-3">
                    <i class="fab fa-github fa-2x"></i>
                </a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function (event) {
            const button = event.target.closest('[data-copy]');
            if (!button) return;
            const text = button.getAttribute('data-copy');
            navigator.clipboard.writeText(text).then(function () {
                const original = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copied';
                setTimeout(function () { button.innerHTML = original; }, 1200);
            });
        });
    </script>
</body>
</html>

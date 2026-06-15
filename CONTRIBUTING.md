# Contributing to QuickPoll

Thanks for taking the time to contribute! 🎉

## Getting started

1. Fork and clone the repository.
2. Set up a local admin password:
   ```bash
   php bin/hash-password.php "dev-password"
   cp config.example.php config.local.php
   # paste the hash into config.local.php
   ```
3. Run the app locally:
   ```bash
   php -S localhost:8000 -t public
   ```

## Development guidelines

- **PHP version:** target PHP **8.2+**.
- **Style:** follow the existing code — `declare(strict_types=1)`, typed properties and
  arguments, `final` classes, constructor promotion, and 4-space indentation.
- **Architecture:** keep controllers thin; put business rules in `src/Survey/*` services and
  data access in repositories. Register new services in
  [`src/Container.php`](src/Container.php).
- **Security:** all SQL must use prepared statements, all output must go through
  `View::escape()`, and every state-changing form must include a CSRF token.
- **No new runtime dependencies** without discussion — staying dependency-free is a goal.

## Before opening a pull request

Lint every PHP file:

```bash
find src public templates bin config.php bootstrap.php -name '*.php' -print0 \
  | xargs -0 -n1 php -l
```

Then:

1. Keep changes focused; one logical change per PR.
2. Describe **what** changed and **why** in the PR description.
3. Reference any related issue (e.g. `Closes #12`).

## Reporting bugs & requesting features

Open an issue with clear steps to reproduce (for bugs) or a concise description of the use
case (for features). Check the [roadmap](README.md#roadmap) first — your idea may already be
listed.

## Security issues

Please do **not** open a public issue for security vulnerabilities. See
[SECURITY.md](SECURITY.md) instead.

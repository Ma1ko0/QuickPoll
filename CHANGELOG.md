# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Poll expiry:** polls can have an optional closing date/time, after which voting is
  disabled and final results are shown.
- **Rate limiting:** persistent, IP-based limits on admin logins (5 / 15 min) and voting
  (10 / min), backed by a new `rate_limits` table and `App\Security\RateLimiter`.
- **Docker support:** `Dockerfile` (PHP 8.3 + Apache), `docker-compose.yml`, `.dockerignore`,
  and an Apache override so the app serves from `public/`.
- Open-source release scaffolding: README, LICENSE (MIT), CONTRIBUTING, SECURITY, and this
  changelog.
- `bin/hash-password.php` CLI helper to generate an admin password hash.
- `config.example.php` template and environment-variable / `config.local.php` based
  configuration.
- Continuous integration workflow that lints all PHP files.

### Changed
- **Redesigned UI** to a clean light theme with an indigo/violet accent (replacing the
  previous dark green/blue look).
- Admin password is now stored as a bcrypt hash and verified with `password_verify()`
  instead of comparing against a hardcoded plaintext value.
- Configuration secrets are no longer committed; they are loaded from the environment or a
  git-ignored local file.
- All UI text standardized to English.

### Database
- `surveys.expires_at` column added (migrated automatically for existing databases).
- New `rate_limits` table.

### Security
- Removed the hardcoded admin password from version control.

## [0.1.0]
- Initial poll creation, voting, and results functionality.

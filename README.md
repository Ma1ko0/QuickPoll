# QuickPoll

A tiny, dependency-free PHP app for creating and sharing polls with real-time results.

Spin up a question, add some options, and share a short link — everyone who opens it can
vote and instantly see the breakdown. Backed by SQLite, so there's no database server to
set up. The whole thing is a few hundred lines of clean, framework-free PHP.

> ⚠️ **Replace the placeholder branding before publishing.** Update the GitHub URL in
> [`templates/layout.php`](templates/layout.php), and add your own `favicon.ico` /
> logo to [`public/`](public/) if you want one (the originals were removed during
> open-sourcing).

## Features

- 📊 **Create polls** with a question and 2+ options from a password-protected admin panel
- ⏰ **Poll expiry** — optionally set a closing date/time; voting stops automatically and final
  results are shown
- 🛡️ **Rate limiting** on voting and admin logins (persistent, IP-based) to curb abuse and
  brute-force attempts
- 🔗 **Shareable short codes** — each poll gets a unique, human-friendly code (e.g. `Kp7mQ2`)
- 🗳️ **One-click voting** with live percentage bars
- 👀 **Real-time results** shown after a visitor votes
- 🔒 **Hashed admin password**, CSRF protection on every form, and prepared SQL statements
- 🐳 **Docker-ready** — `docker compose up` and you're running
- 🪶 **Zero runtime dependencies** — just PHP 8.2+ with the SQLite extension
- 🎨 Clean, responsive light UI (Bootstrap 5 + a sprinkle of custom CSS)

## Requirements

- PHP **8.2** or newer
- PHP extensions: `pdo` and `pdo_sqlite` (bundled with most PHP builds)
- A web server (Apache, Nginx) — or just PHP's built-in server for local use

## Quick start (PHP built-in server)

```bash
# 1. Get the code
git clone https://github.com/your-username/quickpoll.git
cd quickpoll

# 2. Set an admin password (copy the printed hash)
php bin/hash-password.php "choose-a-strong-password"

# 3. Create your local config and paste the hash into it
cp config.example.php config.local.php
#    edit config.local.php -> admin.password_hash

# 4. Run it
php -S localhost:8000 -t public
```

Now open <http://localhost:8000>. The admin panel lives at
<http://localhost:8000/admin.php>.

The SQLite database is created automatically at `data/surveys.sqlite` on first run.

## Quick start (Docker)

```bash
# 1. Generate a password hash and put it in .env
php bin/hash-password.php "choose-a-strong-password"
cp .env.example .env
#    set QUICKPOLL_ADMIN_PASSWORD_HASH=<the hash> in .env

# 2. Build and run
docker compose up --build
```

Open <http://localhost:8080>. The database persists in the `quickpoll-data` Docker volume.

> No PHP installed locally to run `hash-password.php`? You can also generate the hash inside
> the container: `docker compose run --rm app php bin/hash-password.php "your-password"`.

## Configuration

Configuration is resolved in [`config.php`](config.php) with the following precedence:

| Setting        | Environment variable             | `config.local.php` key       | Default                      |
| -------------- | -------------------------------- | ---------------------------- | ---------------------------- |
| Admin password | `QUICKPOLL_ADMIN_PASSWORD_HASH`  | `admin.password_hash`        | _(none — login disabled)_    |
| Database path  | `QUICKPOLL_DATABASE_PATH`        | `database.path`              | `data/surveys.sqlite`        |

- **Local development:** copy `config.example.php` to `config.local.php` (git-ignored) and
  paste your password hash.
- **Production:** prefer environment variables so no secret ever touches the filesystem.

> The admin password is **never** stored in plaintext. `bin/hash-password.php` produces a
> bcrypt hash via PHP's `password_hash()`, and logins are verified with `password_verify()`.

## Deployment

QuickPoll is designed so the document root points at [`public/`](public/), keeping the
application code, config, and database outside the web root.

**Apache:** the included [`.htaccess`](public/.htaccess) routes unknown paths to
`index.php`. Point your virtual host's `DocumentRoot` at the `public/` directory and ensure
`AllowOverride All` is set.

**Nginx:** a minimal server block:

```nginx
server {
    root /var/www/quickpoll/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

Make sure the `data/` directory is **writable** by the web server user, and that it is not
publicly reachable.

## Project structure

```
quickpoll/
├── public/              # Web root (the only publicly served directory)
│   ├── index.php        # Home / poll overview
│   ├── survey.php       # Vote on a poll by short code
│   └── admin.php        # Admin panel (create / delete polls)
├── src/
│   ├── Auth/            # Admin authentication (hashed password)
│   ├── Controller/      # Home, Survey, Admin controllers
│   ├── Database/        # PDO connection + schema migrator
│   ├── Http/            # URL building / redirects
│   ├── Security/        # CSRF tokens, short-code generation, rate limiter
│   ├── Survey/          # Domain models, repository, service, vote tracking
│   ├── View/            # Tiny template renderer
│   └── Container.php    # Hand-rolled dependency-injection container
├── templates/           # PHP view templates
├── data/                # SQLite database lives here (git-ignored)
├── bin/hash-password.php # CLI helper to generate an admin password hash
├── docker/              # Apache override for the Docker image
├── Dockerfile           # PHP 8.3 + Apache image
├── docker-compose.yml   # One-command local stack
├── config.php           # Loads config from env / config.local.php
├── config.example.php   # Template for config.local.php
└── bootstrap.php        # Autoloader + container bootstrap
```

### How it fits together

A request hits a thin script in `public/`, which loads
[`bootstrap.php`](bootstrap.php) (PSR-4 autoloader + session + `Container`). The container
wires up the relevant controller, which talks to `SurveyService` /
`SurveyRepository` and renders a template through `View`. The schema is created
automatically by `SchemaMigrator` on the first database connection.

## Security notes

- Admin password is hashed (bcrypt) and verified with `password_verify()`.
- All state-changing forms (login, create, delete, vote) carry a CSRF token.
- All SQL uses prepared statements; all output is escaped via `View::escape()`.
- **Rate limiting** (persistent, IP-based) protects admin logins (5 attempts / 15 min) and
  voting (10 votes / min). The client IP comes from `REMOTE_ADDR`; if you run behind a
  reverse proxy, make sure it sets that correctly (and don't trust forwarded headers blindly).
- **Vote de-duplication is session-based** — it stops casual double-voting but is not a
  strong guarantee (clearing cookies allows another vote). See
  [issues / roadmap](#roadmap) for hardening ideas.

Found a vulnerability? Please see [SECURITY.md](SECURITY.md).

## Roadmap

Ideas and good first contributions:

- [ ] Stronger duplicate-vote protection (IP + cookie token)
- [ ] Edit existing polls
- [ ] CSV / JSON export of results
- [ ] Multiple admin accounts
- [ ] A small automated test suite (PHPUnit)

## Contributing

Contributions are welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## License

[MIT](LICENSE) © QuickPoll contributors

# Render and TiDB Cloud Starter deployment guide

This repository is prepared for a **later guided deployment session**. The deployment has not been started. GitHub holds source code; Render will run the CodeIgniter 4 app; TiDB Cloud Starter will hold its MySQL-compatible data. Free services are appropriate for a class demonstration and evaluation, not dependable production infrastructure.

## What is prepared

- `Dockerfile` installs production Composer dependencies and the PHP `mysqli`, `intl`, `mbstring`, and `gd` extensions. Apache serves only `public/`.
- `render.yaml` describes a **Free** Render Docker web service with automatic deploys off and an HTTP health check at `/health`. It omits paid-plan settings such as a custom shutdown delay and a persistent disk.
- The container binds to Render's `PORT` on all interfaces. Its startup script only starts Apache; it never changes the database.
- `DB_*` environment variables select the TiDB connection. `DB_TLS=true` enables certificate-verified TLS using the CA bundle path in `DB_SSL_CA`.
- `APP_BASE_URL` or Render's `RENDER_EXTERNAL_URL` sets absolute links. `CI_ENVIRONMENT=production` hides debug output and enables secure cookies.
- Product and staff image uploads remain available on the hosted demo. The app converts valid JPG/PNG files to bounded JPEGs and stores production image bytes in TiDB's `uploaded_images` table. Local development keeps images on disk.

## Required settings for the future session

Create a TiDB Cloud Starter instance and use its **Public / Standard** connection details. The instance username includes TiDB's generated prefix, such as `<prefix>.root`. TiDB's public endpoint requires TLS. Save the database password in TiDB or a password manager; **never paste it into GitHub or this repository**.

In Render, create a Blueprint from this repository's `render.yaml` only when ready to deploy. The Blueprint prompts for these values:

| Key | Value | Secret? |
| --- | --- | --- |
| `DB_HOST` | TiDB host from Connect dialog | No |
| `DB_PORT` | TiDB port, normally `4000` | No |
| `DB_NAME` | TiDB database name, for example `test` or a database you created | No |
| `DB_USER` | Full prefixed TiDB username | No |
| `DB_PASSWORD` | TiDB user password | **Yes** |
| `DB_TLS` | `true` | No |
| `DB_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` in the Debian image | No |
| `CI_ENVIRONMENT` | `production` | No |
| `APP_BASE_URL` | Optional; omit for Render's assigned URL, or set a full `https://.../` custom-domain URL | No |

The Blueprint sets nonsecret values and prompts for the connection details. Render supplies `RENDER_EXTERNAL_URL`. Do not copy the local `.env` or `.local-credentials` into Render. Use fresh account passwords when creating cloud demo users; the local professor password is intentionally simple and should not be reused on a public site.

When running migration commands from macOS against TiDB Cloud, set `DB_SSL_CA=/etc/ssl/cert.pem` in that terminal. Keep the Debian path only inside the Render container.

## One-time database setup

The container **does not run migrations or seeders on startup**. After creating TiDB Cloud Starter, use a local terminal connected to its public endpoint, or another controlled one-time shell, with the environment values above. From this repository:

```bash
composer install
php spark migrate --all
php spark migrate:status
```

CodeIgniter records applied migrations, so repeating `migrate --all` applies only new ones. Do not use `migrate:refresh`, `migrate:rollback`, or destructive resets on the live database. Verify the schema on TiDB Cloud before deploying the Render service, including the new `uploaded_images` table. The migrations use MySQL-compatible types, including `MEDIUMBLOB` for prepared JPEGs. They use lowercase table and column identifiers. Staff usernames are normalized to lowercase in the app, so uniqueness is stable even when collations differ. Checkout uses a transaction and a `SELECT ... FOR UPDATE` lock; this still needs a real TiDB smoke test in the deployment session.

For a **new, empty database only**, optional demo data can be added once. Set four **new, distinct** passwords of at least 12 characters as private environment variables before running the first seeder:

```bash
php spark db:seed PosSeeder
php spark db:seed MidtermProductsSeeder
php spark db:seed TaskSystemSeeder
```

The first command reads `MIDTERM_INITIAL_PASSWORD`, `MIDTERM_HOWARD_PASSWORD`, `MIDTERM_GERARD_PASSWORD`, and `MIDTERM_PROFESSOR_PASSWORD`. Do not put their values in shell history, GitHub, `render.yaml`, or a shared document. Seeding is never automatic; do not rerun seeders on a populated database. If you are moving the existing local records rather than starting from demo data, plan a separate reviewed data migration.

## Local development and Docker checks

Copy `.env.example` to `.env`, fill in local MySQL settings, then run `composer install`, `php spark migrate --all`, and `php spark serve --port 8080`. Local uploads remain enabled.

When Docker is installed, build and run the container locally against a **separate test database**:

```bash
docker build -t ledgerline-midterm-pos .
cp .env.example .env.docker
# Edit .env.docker to use a separate local test database and its credentials.
docker run --rm -p 10000:10000 --env-file .env.docker -e DB_HOST=host.docker.internal -e CI_ENVIRONMENT=development -e APP_BASE_URL=http://localhost:10000/ ledgerline-midterm-pos
curl -i http://localhost:10000/health
```

On macOS, a container cannot reach the host's MySQL service at `127.0.0.1`; the command overrides it with `host.docker.internal`. Do not point this test at the working class database unless intended. Run `vendor/bin/phpunit --no-coverage` for repository tests; run `php spark routes` to inspect routes. `GET /health` returns `ok` only when the database accepts `SELECT 1`, otherwise a generic HTTP 503 response.

## Files, sessions, and free-tier limits

Render's free service filesystem is ephemeral. Uploaded product images and staff avatars are therefore converted to JPEGs and saved in TiDB, then served from `/media/{id}`. The form accepts JPG/PNG files up to 2 MB; prepared images also have a 2 MB cap. Replacing an image removes its previous stored copy. Existing local uploads are excluded from Git and will not appear on Render; move those files separately if their records are migrated. Bundled SVG product art and illustrated staff portraits remain available. File-backed login sessions may end after a cold start or redeploy; users can sign in again.

Render free web services can spin down when idle and take time to wake. Render may also suspend a free service that initiates unusually high traffic to an external database; keep this class demo's usage modest. TiDB Cloud Starter has free usage quotas for storage and request units; image uploads and views count toward them. Keep only a small set of demo images and monitor usage. Exceeding a free limit can throttle or block operations. Check the provider dashboards and current plan terms during deployment.

## Troubleshooting in the deployment session

- **`/health` returns 503:** check `DB_HOST`, port, prefixed username, password, database name, TLS setting, and CA path. Do not log or share the password.
- **The app shows a database/table error:** run and inspect migrations on the intended TiDB database; the Docker startup deliberately does not create tables.
- **Sign-in fails on a fresh database:** seed once with new passwords, or create accounts through a controlled bootstrap path. GitHub does not contain local account password hashes.
- **Cookies or redirects look wrong:** confirm `CI_ENVIRONMENT=production` and the public HTTPS base URL. Render terminates HTTPS and forwards `X-Forwarded-Proto: https`; Apache marks those requests secure before CodeIgniter sends secure cookies. A direct plain-HTTP production request is intentionally unsupported.
- **New image upload fails:** confirm `uploaded_images` exists, database quota remains, and the file is a JPG/PNG no larger than 2 MB. Uploaded images are stored in TiDB on Render, not in its local filesystem.
- **Older images disappear:** image paths from the local development database refer to files excluded from Git. Migrate those files separately or upload them again through the hosted forms.
- **Build fails:** check Render Docker logs and that Composer can install from `composer.lock`; no runtime dependency should require the local `vendor/` folder.
- **Blueprint reports a paid-plan field:** pull the latest `main` branch before retrying. The Free Blueprint must not include `maxShutdownDelaySeconds` or a persistent disk.

## Provider documentation

- [Render Docker web services](https://render.com/docs/docker), [Blueprint settings](https://render.com/docs/blueprint-spec), and [ephemeral filesystems](https://render.com/docs/deploys)
- [TiDB Cloud Starter connections and TLS](https://docs.pingcap.com/tidbcloud/secure-connections-to-serverless-clusters/), [foreign keys](https://docs.pingcap.com/tidbcloud/foreign-key/), and [plan quotas](https://docs.pingcap.com/tidbcloud/select-cluster-tier/)

# Ledgerline Print & Supply — Midterm POS

A CodeIgniter 4 point-of-sale application for a neighborhood print and stationery shop. Staff can manage products, customers, and accounts; record sales; and track inventory. The earlier task board remains available as an extra feature. This midterm copy uses its own MySQL database and does not change the TSA2 project.

Midterm group: **Jian Edward Acob, Howard Mac Callanta, and Gerard Eric Doroja**. Ledgerline began with Jian's earlier coursework and is presented here as the group's shared midterm project. Staff portraits are illustrations rather than photographs.

## What works

- Product create, list, edit, and archive; validated JPG/PNG upload prepared as a display-sized JPEG
- Customer and staff create, list, edit, and delete; linked records are archived to preserve sales history
- Staff avatar upload, password hashing, sign-in, and protected management routes
- On Render, prepared uploads are stored in TiDB so they survive service restarts; local development keeps files on disk
- Separate group-member accounts and a professor demo account for local assessment
- Checkout with an optional customer, server-calculated total, and a database transaction that decreases stock
- Clear rejection when quantity is invalid or exceeds available stock
- Sales history with product, customer or walk-in, staff, quantity, total, and date
- Responsive layout, dark mode, and the retained daily task board

## Local setup

Requirements: PHP 8.2+, Composer, MySQL 8 or MariaDB, and PHP extensions `mysqli`, `intl`, `mbstring`, and `gd` or `imagick`.

```bash
composer install
mysql -u root -e 'CREATE DATABASE ledgerline_midterm_pos CHARACTER SET utf8mb4;'
cp .env.example .env
```

In `.env`, set the local `DB_*` values and leave `DB_TLS=false`. Keep `.env` private. The older CodeIgniter `database.default.*` settings also remain supported for existing local installations.

For a **fresh database only**, run:

```bash
php spark migrate --all
export MIDTERM_INITIAL_PASSWORD='your-own-private-password-of-at-least-12-characters'
export MIDTERM_HOWARD_PASSWORD='a-different-private-password-of-at-least-12-characters'
export MIDTERM_GERARD_PASSWORD='another-different-private-password-of-at-least-12-characters'
export MIDTERM_PROFESSOR_PASSWORD='a-fourth-password-of-at-least-12-characters'
php spark db:seed PosSeeder
unset MIDTERM_INITIAL_PASSWORD
unset MIDTERM_HOWARD_PASSWORD
unset MIDTERM_GERARD_PASSWORD
unset MIDTERM_PROFESSOR_PASSWORD
php spark db:seed MidtermProductsSeeder
php spark db:seed TaskSystemSeeder
php spark serve --port 8080
```

Open `http://localhost:8080/`. Group-member usernames are `jian`, `howard`, and `gerard`; the professor demo username is `professor`. Each uses the distinct password set during setup. Isaiah and Aaron remain sample shop staff accounts with random initial passwords, which can be reset through staff management. Do not rerun sample seeders on a populated database. The local copy prepared for review has its own private credentials file, `.local-credentials`, which is excluded from version control.

Amiel and Sean are demonstration customer records. Their phone numbers were carried over from the Howard and Gerard customer rows in the earlier project at the group's request; their email addresses are deliberately nonworking `example.invalid` values.

## Main routes

| Route | Purpose |
| --- | --- |
| `/` and `/tasks` | Public Today page and task board |
| `/login` | Staff sign-in |
| `/products`, `/products/new` | Product inventory and creation |
| `/sales`, `/sales/new` | Sales history and checkout |
| `/customers`, `/customers/new` | Customer management |
| `/users`, `/users/new` | Staff management |
| `/profile`, `/about` | Group team and shop information |

Management routes require a staff session. Write actions use POST and CSRF protection. The `sales` table links to `products`, `customers`, and `users` with foreign keys. A product is locked during checkout, and recording the sale and reducing stock happen in one transaction.

## Repository notes

This repository contains source code and sample seeders. It is not yet a hosted application; GitHub cannot run the PHP/MySQL site by itself. `.env`, local credentials, uploaded files, dependencies, and runtime data are excluded from version control. The professor account has the same management access as other staff accounts, so use a fresh password when deploying to a shared server.

See [Render and TiDB deployment preparation](docs/deployment-render-tidb.md) for the future guided deployment session. No cloud service is created by the files in this repository alone.

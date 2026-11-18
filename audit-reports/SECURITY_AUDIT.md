# Security Audit — topblogtech.com

**Audit date:** 2026-11-18 · **Scope:** Git repository, web root, live site (public probes only), WordPress core/plugins/theme, local copy of the production database.
**Note:** This repository is **public on GitHub**. This report intentionally contains no credential values, usernames, database names or server paths.

Status legend: **FIXED** (in code, verified locally) · **DEPLOY** (fixed in code, takes effect after the cPanel deployment) · **OWNER** (needs action only the owner can take) · **BLOCKED** (needs access I do not have)

---

## 1. Summary

| # | Severity | Finding | Status |
|---|---|---|---|
| S1 | **Critical** | `wp-config.php` (database password + all 8 authentication keys/salts) is committed to the **public** GitHub repository and present in its history. | Untracked + ignored in this commit · **OWNER: rotate DB password and salts** |
| S2 | **Critical** | Three full database dumps (posts, options, users table **including the admin password hash and email**, plugin keys) committed to the public repository history. | Untracked + ignored · **OWNER: change admin password, make repo private** |
| S3 | **Critical** | `topblogtech_live_update.zip` (contains a full SQL dump) was **publicly downloadable** from the live site (HTTP 200, 295 KB). | DEPLOY (`.htaccess` now blocks archives) · **OWNER: move file out of web root immediately** |
| S4 | **Critical** | `topblogtech_live_database.sql.bak_pre_adsense_remediation_20261113` (full DB dump) was **publicly downloadable** from the live site (HTTP 200, 426 KB). The old rule only matched names *ending* in `.sql`. | DEPLOY · **OWNER: move file out of web root immediately** |
| S5 | High | Administrator account uses a predictable default username; its password hash is in the public dumps. | **OWNER**: new admin with unique username + strong password + 2FA, then delete old account |
| S6 | High | Internal audit reports (`SECURITY_AUDIT.md`, etc.) were publicly readable on the website. | FIXED/DEPLOY — moved to `audit-reports/` (denied by `.htaccess`) and all `*.md` blocked |
| S7 | High | Site reachable over plain `http://` with HTTP 200 (no HTTPS redirect). | DEPLOY — loop-safe redirect using Cloudflare's `X-Forwarded-Proto` · OWNER: enable Cloudflare "Always Use HTTPS" |
| S8 | Medium | `/wp-json/wp/v2/users` exposes the author account to anonymous visitors. | DEPLOY — endpoint hidden for logged-out users (editor unaffected) |
| S9 | Medium | No rule preventing PHP execution in `wp-content/uploads/`. | DEPLOY — scripts in uploads return 403 |
| S10 | Medium | `.git/`, `tools/`, logs, `*.bak*`, `*.tar.*`, `*.ini`, `*.md`, `package.json` etc. not blocked. | DEPLOY — all return 403/404 (tested) |
| S11 | Medium | Comments open by default (`default_comment_status=open`) while comments are not displayed → silent spam intake via `wp-comments-post.php`. | DEPLOY (DB script closes comments/pings; mu-plugin forces closed) |
| S12 | Medium | Parent theme version faked to `99.9.9` with `Update URI: false` to block updates, because site code lived inside the parent theme. | Code moved to theme-independent mu-plugin. Theme updates still blocked because templates/CSS remain customised — **recommended follow-up: child theme** |
| S13 | Low | `X-XSS-Protection` (deprecated) set; no `Permissions-Policy`. | FIXED — removed / added |
| S14 | Low | `backuply_config_keys` option is present in the public dumps. | **OWNER**: regenerate Backuply keys / re-save Backuply settings |
| S15 | Info | No MX/SPF/DMARC DNS records for the domain (see trust report). | **OWNER** |

## 2. Malware / integrity checks (local copy)

| Check | Result |
|---|---|
| WordPress 7.1 core vs official checksums (api.wordpress.org) | **PASS** — 3,338 files match, 0 modified, 0 missing, 0 unexpected files in `wp-admin`/`wp-includes`/root |
| 8 plugins vs wordpress.org per-version checksums (akismet, backuply, formlayer, gosmtp, loginizer, pagelayer, siteseo, speedycache) | **PASS** — 0 modified, 0 extra PHP files |
| PHP/phtml/phar files in `wp-content/uploads` | **PASS** — none |
| Obfuscation patterns (`eval(base64_decode`, `gzinflate`, `assert($_`, `/e` modifier, `create_function`, `fromCharCode`) in themes, mu-plugins, drop-ins, root | **PASS** — none |
| `<script>` / `<iframe>` injected in posts; `eval`/`base64_decode`/`<script` in options | **PASS** — none |
| Users / roles | 1 user, administrator — no rogue accounts |
| Production server files | **NOT TESTED** — run `wp core verify-checksums` and `wp plugin verify-checksums --all` on the server (commands in `CPANEL_DEPLOYMENT.md`) |

## 3. Root causes

1. **The web root is the Git working tree.** Anything committed (dumps, config, reports) is both public on GitHub and served by Apache unless explicitly denied.
2. **Database changes were shipped as full SQL dumps** committed to the repo and imported on production. This put credentials and user data into a public repo and left dumps in the web root.
3. **Denylist too narrow** (`.*\.sql$` only).

## 4. What changed (this commit)

- `.gitignore` — blocks `wp-config.php`, `*.sql`, `*.sql.*`, `*.zip`, `*.tar*`, `*.bak*`, logs, caches, backup folders.
- `git rm --cached` — `wp-config.php` and all 4 dump/archive files removed from tracking (local copies kept; checksum-verified copies stored outside the repo).
- `.htaccess` — broad deny for config/backup/archive/log/doc files, `.git` & tooling dirs → 404, uploads script execution → 403, HTTPS redirect, `Permissions-Policy`, HTML no longer browser-cached for 1 month.
- `wp-content/mu-plugins/topblogtech-core.php` — xmlrpc off, generator removed, `?author=` enumeration blocked, users REST endpoint hidden for anonymous users, comments/pings closed, contact form with nonce + honeypot + timing check + per-IP rate limit (3/hour), no data stored.
- `tools/` — `.htaccess` deny + every script exits unless run from CLI.

## 5. Verification (local Apache 2.4 + PHP 8.2 with the real `.htaccess`)

19 sensitive paths requested → all **403/404** (`wp-config.php`, `*.sql`, `*.sql.bak_*`, `*.zip`, `*.tar.gz`, `*.md`, `error_log`, `*.bak`, `.git/config`, `tools/*`, `audit-reports/*`, `uploads/*.php`, `xmlrpc.php`, `readme.html`, `php.ini`, `.user.ini`, `package.json`). Normal assets → 200. `X-Forwarded-Proto: http` → 301 to https. **PASS**.
Contact form: invalid nonce → rejected; invalid email → rejected; honeypot & <3 s submissions → silently dropped; 4th submission/hour → rate-limited. **PASS**. Real email delivery: **NOT TESTED** (no mail server locally).

## 6. Owner actions (in order — see CPANEL_DEPLOYMENT.md for commands)

1. **Now:** move the two downloadable dumps out of the web root (S3, S4).
2. **GitHub → Settings → General → Danger Zone → Change visibility → Private.** History still contains the secrets, so step 3 is mandatory either way.
3. **Rotate:** database user password (cPanel → MySQL Databases), all 8 WordPress keys/salts (new set from `https://api.wordpress.org/secret-key/1.1/salt/`), WordPress admin password; enable 2FA (Loginizer supports it); create a new administrator with a non-default username and delete the old one (attribute content to the new account).
4. Regenerate Backuply keys; check GoSMTP settings for any stored SMTP password and change it if one is set.
5. Optional, only after making the repo private and rotating: purge history with `git filter-repo`/BFG. This **requires a force-push**, which this task did not perform by instruction. Rotation (step 3) is what actually neutralises the leak.
6. Remove unused plugins (Hello Dolly, Akismet [inactive]; Pagelayer & FormLayer if not needed — the contact page no longer uses FormLayer).

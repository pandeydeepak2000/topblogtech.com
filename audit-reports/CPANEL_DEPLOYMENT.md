# cPanel Production Deployment — topblogtech.com

For: the site owner, running commands in **cPanel → Terminal**. Read each step before running it.
Assumptions (check them in step 1): the site's Git working tree is the web root at `~/topblogtech.com` (this matches the cPanel paths in the repo's `.user.ini`), its remote is this GitHub repo, and it is on branch `main`.

> ⚠️ **Do NOT** follow the old instructions in `archive-2026-11-13/` (`git reset --hard`, importing `topblogtech_live_database.sql`). Importing an old dump overwrites everything changed in wp-admin since that dump and re-exposes data.

---

## 0. Emergency — do this first, even before deploying

Two database dumps are publicly downloadable right now. Move them **out of the web root** (they are kept, not deleted):

```bash
mkdir -p ~/private-backups && chmod 700 ~/private-backups
cd ~/topblogtech.com
ls -la topblogtech_* 2>/dev/null          # see what is there
mv -n topblogtech_*.sql* topblogtech_*.zip ~/private-backups/ 2>/dev/null
ls -la ~/private-backups/
curl -s -o /dev/null -w "%{http_code}\n" https://topblogtech.com/topblogtech_live_update.zip   # expect 404 (or 403)
```
Then purge Cloudflare cache (Cloudflare dashboard → Caching → Configuration → **Purge Everything**) so cached copies are gone.

## 1. Inspect the server (read-only)

```bash
cd ~/topblogtech.com
pwd
git remote -v                 # should show github.com/pandeydeepak2000/topblogtech.com
git branch --show-current     # should be: main
git status --short            # note anything listed as M (modified) or D (deleted)
git log -1 --oneline
php -v                        # PHP CLI version (8.1+ recommended)
wp --info 2>/dev/null | head -3 || echo "WP-CLI not available (that is OK)"
```
If `git status` shows modified tracked files **other than** `wp-config.php` or the dump files, stop and save them first: `git diff > ~/private-backups/server-local-changes-$(date +%F).patch`.

## 2. Backups (verify before continuing)

```bash
cd ~/topblogtech.com
git rev-parse HEAD > ~/private-backups/pre-deploy-head.txt
cp -p wp-config.php ~/private-backups/wp-config.php.$(date +%F)
cp -p .htaccess   ~/private-backups/htaccess.$(date +%F)
```
**Database backup** — use one of:
- WP-CLI: `wp db export ~/private-backups/db-$(date +%F-%H%M).sql`
- or cPanel → **Backup** → *Download a MySQL Database Backup* (choose this site's database) and keep the file.
- or mysqldump (it asks for the password; read DB name/user with `grep -E "DB_(NAME|USER)" wp-config.php`):
  `mysqldump -u DB_USER -p DB_NAME > ~/private-backups/db-$(date +%F-%H%M).sql`

Verify: `ls -la ~/private-backups/` — the `.sql` file must be several hundred KB or more.

## 3. Pull the code (fast-forward only)

`wp-config.php` is no longer tracked by Git (it held secrets in a public repo). A normal pull would **delete** it, so it is moved aside and restored in the same command. The site is unavailable for ~1–2 seconds.

```bash
cd ~/topblogtech.com
git fetch origin
git log --oneline HEAD..origin/main          # review what will arrive
mv wp-config.php ~/private-backups/wp-config.live \
 && git merge --ff-only origin/main ; \
 cp -p ~/private-backups/wp-config.live wp-config.php && ls -la wp-config.php
```
- If the merge prints *"Not possible to fast-forward"* the server has local commits: **stop** and send me `git log --oneline -5` and `git status` output (wp-config is already restored by the command above).
- If it prints *"untracked working tree files would be overwritten"*, move the listed files to `~/private-backups/` and run the block again.

Confirm: `git log -1 --oneline` matches GitHub, and `git status --short` is empty (wp-config is now ignored).

## 4. Permissions

```bash
cd ~/topblogtech.com
chmod 640 wp-config.php
chmod 644 .htaccess wp-content/mu-plugins/topblogtech-core.php
find wp-content/mu-plugins tools audit-reports -type d -exec chmod 755 {} \;
php -l wp-content/mu-plugins/topblogtech-core.php
```
If the site shows a 403/500 after `chmod 640`, your host runs PHP as a different user — use `chmod 644 wp-config.php` instead.

## 5. Quick health check (code only)

```bash
for u in / /about-us/ /contact-us/ /privacy-policy/ /getting-google-adsense-approval-fast/ /sitemaps.xml /robots.txt; do
  printf "%-45s %s\n" "$u" "$(curl -s -o /dev/null -w '%{http_code}' https://topblogtech.com$u)"; done
for u in /wp-config.php /README.md /tools/ /audit-reports/ /.git/config /xmlrpc.php; do
  printf "%-45s %s (want 403/404)\n" "$u" "$(curl -s -o /dev/null -w '%{http_code}' https://topblogtech.com$u)"; done
curl -s -o /dev/null -w "http -> %{http_code} %{redirect_url}\n" http://topblogtech.com/
```
Expected: content URLs 200, protected URLs 403/404, http → 301 to https. If the homepage returns a redirect loop or 500, go to **Rollback (§10)**.

## 6. Database content remediation (the actual content fixes)

The script updates only what is listed in `CONTENT_AUDIT.md`. Post IDs, slugs, publish dates, authors, featured images and categories of posts are untouched. Before writing it saves a JSON backup to `~/tbt-remediation-backups/`.

```bash
cd ~/topblogtech.com
php tools/content-remediation/remediate.php            # DRY RUN — changes nothing
```
Expected last lines: `33 posts/pages to update, 5 options, 11 empty categories, 0 blocked`.
- If any post shows **BLOCKED**, that post was edited in wp-admin after 2026-11-17; the script will skip it. Send me the output.
- If the PHP CLI is too old, use the full path to a newer PHP, e.g. `/opt/cpanel/ea-php82/root/usr/bin/php tools/content-remediation/remediate.php`.

Apply:
```bash
php tools/content-remediation/remediate.php --apply
php tools/content-remediation/remediate.php            # run again: must say "0 posts/pages to update"
```
Note the printed backup path (needed for rollback).

## 7. Caches

- The script purges SpeedyCache automatically (if it says "purge manually": wp-admin → SpeedyCache → Delete Cache).
- Cloudflare → Caching → **Purge Everything**.
- Cloudflare → SSL/TLS → **Edge Certificates → Always Use HTTPS: On**; SSL/TLS mode **Full (strict)** if the server has a valid certificate (cPanel AutoSSL), otherwise **Full**.

## 8. Post-deployment checks

```bash
P=https://topblogtech.com/youtube-algorithm-secrets-to-get-more-views/
curl -s $P | grep -c 'placeholder-'                         # want 0
curl -s $P | sed -n '/<body/,$p' | grep -o '\\n' | wc -l     # want 0 (or only inside scripts)
curl -s $P | grep -c '<meta name=.robots'                   # want 1
curl -s $P | grep -c 'rel=.canonical'                       # want 1
curl -s https://topblogtech.com/contact-us/ | grep -c 'tbt-contact-form'   # want 1
curl -s https://topblogtech.com/category-sitemap1.xml | grep -c '<loc>'    # want 7
```
Optional integrity checks (only if WP-CLI exists): `wp core verify-checksums` and `wp plugin verify-checksums --all`.
In a browser (phone + desktop): open an article, About, Contact; **send yourself a test message through the contact form** and confirm it arrives in the admin email inbox (wp-admin → Settings → General shows that address). If it does not arrive, configure GoSMTP (wp-admin → GoSMTP) with your mailbox's SMTP settings.

## 9. Credential rotation (mandatory — the repo is public)

1. cPanel → **MySQL Databases** → *Current Users* → change the password of this site's DB user → put the new password in `wp-config.php` (`DB_PASSWORD`) with the cPanel File Manager editor → reload the site.
2. Open `https://api.wordpress.org/secret-key/1.1/salt/` → replace the 8 `define(...KEY/SALT...)` lines in `wp-config.php` (everyone is logged out).
3. wp-admin → Users → create a **new Administrator** with a non-default username, strong password; log in as it; delete the old admin account and **attribute all content to the new user**. Enable Loginizer 2FA.
4. Backuply → re-save settings / regenerate keys; GoSMTP → change any stored SMTP password.
5. GitHub → repo **Settings → Change visibility → Private** (recommended).

## 10. Rollback

**Database content** (restores the 33 posts/pages, the 5 options, and recreates the 11 categories):
```bash
cd ~/topblogtech.com
php tools/content-remediation/remediate.php --rollback=$HOME/tbt-remediation-backups/tbt-remediation-YYYYMMDD-HHMMSS.json          # dry run
php tools/content-remediation/remediate.php --rollback=$HOME/tbt-remediation-backups/tbt-remediation-YYYYMMDD-HHMMSS.json --apply
```
Full DB restore (last resort, **overwrites the whole database**): import the step-2 `.sql` via phpMyAdmin.

**Code** (`reset --keep` refuses to discard local changes; wp-config and uploads are untracked and are not touched):
```bash
cd ~/topblogtech.com
git reset --keep $(cat ~/private-backups/pre-deploy-head.txt)
ls -la wp-config.php || cp -p ~/private-backups/wp-config.live wp-config.php
cp -p ~/private-backups/htaccess.$(date +%F) .htaccess     # only if .htaccess is the problem
```
Then purge SpeedyCache + Cloudflare.

## 11. DNS (owner, Cloudflare)

No MX/SPF records exist, so mail to `contact@topblogtech.com` cannot be delivered. In cPanel → **Email Routing / Zone Editor** get the MX target, then in Cloudflare DNS add: `MX @ → <cPanel mail host> (priority 0)`, the SPF `TXT` record cPanel shows (Email Deliverability page), DKIM, and a DMARC record (`_dmarc` TXT `v=DMARC1; p=none; rua=mailto:contact@topblogtech.com`). Then send a test email to `contact@topblogtech.com` from Gmail.

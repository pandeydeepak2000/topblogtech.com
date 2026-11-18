# AdSense Readiness Report — topblogtech.com

**Date:** 2026-11-18 · **Prepared for:** the site owner · **No approval is guaranteed — Google decides.**
Related: [SECURITY_AUDIT.md](SECURITY_AUDIT.md) · [SEO_INDEXING_AUDIT.md](SEO_INDEXING_AUDIT.md) · [CONTENT_AUDIT.md](CONTENT_AUDIT.md) · [CPANEL_DEPLOYMENT.md](CPANEL_DEPLOYMENT.md)

## 1. Bottom line

Before this audit the live site had three problems that alone make an AdSense rejection very likely: **every article displayed literal `\n` text and three broken images**, the content contained **fabricated statistics, fake testing claims and a fictitious editorial team/address**, and **full database backups were publicly downloadable**. All three are fixed in this repository and rehearsed end-to-end on a copy of the production database, but **nothing is live until you deploy** (code via `git pull`, content via the remediation script).

| Area | Status now (repo) | After you deploy + owner actions |
|---|---|---|
| Technical readiness | **NEEDS WORK** (not deployed) | PASS (expected; verify with §8 checks) |
| Content readiness | **NEEDS WORK** | NEEDS WORK — accurate now, but thin on first-hand value (see §5) |
| Trust / policy readiness | **NEEDS WORK** | PASS once MX/email works and founder facts confirmed |
| Indexing readiness | **NEEDS WORK** | Depends on Google re-crawl; *GSC data not available* |
| Security readiness | **BLOCKED** (secrets exposed in public repo) | PASS after credential rotation + dumps moved |

**Recommendation:** deploy, rotate credentials, fix email DNS, wait until Google has re-crawled the fixed pages (check in URL Inspection, typically 1–3 weeks), strengthen 8–10 key articles with first-hand material, *then* apply.

## 2. Issue totals

| Category | Found | Fixed in repo & tested locally | Owner-only |
|---|---|---|---|
| Content (33 items, see CONTENT_AUDIT) | 354 | 354 (928 validated edits, pending deploy) | 8 confirmations (not counted as issues) |
| Security | 15 | 10 (+ S12 mitigated) | 5 (rotate secrets, admin account, Backuply keys, repo visibility, MX) |
| SEO / indexing | 15 (+2 informational) | 15 | — |
| Performance | 7 | 7 | — |
| Design / UI / trust | 4 | 3 | 1 (MX records) |
| **Total** | **395** | **389** | **6** |

## 3. AdSense eligibility & policy checklist

| Requirement (Google AdSense Help / Publisher Policies) | Before | After this work |
|---|---|---|
| Original, valuable content that follows the program policies | Fabricated stats, fake tests, broken rendering | Fact-checked, sourced, honest about method; still mostly secondary research |
| No misleading claims / misrepresentation (Publisher Policies) | Fake team, fake US address, fake affiliate programs, "tested" claims | Removed; one-person site disclosed |
| Site navigation, working pages, no placeholders | 78 broken images, 7 broken internal links, contact form not rendering, "form may not work" note, newsletter box without a form | All fixed; 0 broken images/links in 105 renders |
| Privacy policy disclosing cookies/third parties | Claimed a TCF CMP that did not exist | Accurate: GA4 + Consent Mode, Cloudflare, Loginizer, contact form; future AdSense cookies described conditionally |
| Contact information that works | Email domain has **no MX record** → mail undeliverable | Working contact form (emails admin) ✓; **MX: owner action** |
| Mobile usability | Horizontal scroll on every page at 390 px | 0 overflow at 390/820/1366 px |
| Site reachable, crawlable by Google & Mediapartners-Google | Yes (robots allows both) | Yes |
| Restricted content (finance/crypto) handled responsibly | Guarantees, no risk notes | Risk/"not advice" notes, regulator warnings |
| Affiliate / advertising disclosure | False affiliate claims | Truthful: no affiliate links today; how they will be disclosed |
| Consent for EEA/UK/CH users | None | GA4 Consent Mode v2 default **denied** in EEA/UK/CH. **Before ads serve there**, enable AdSense **Privacy & messaging → European regulations message** (Google-certified CMP, IAB TCF) — or install another Google-certified CMP |
| ads.txt | Not present | **Not created** (requires your real publisher ID — never invented). After approval: AdSense → Sites → ads.txt snippet → create `ads.txt` in the web root with exactly that line |
| AdSense verification code | Not present | Not added (needs your account). Add the snippet AdSense gives you (or use Site Kit) at application time |

## 4. What was changed (files)

| File | Change |
|---|---|
| `wp-content/mu-plugins/topblogtech-core.php` (new) | Site functionality moved out of the parent theme: GA4 + Consent Mode v2, Search Console verification, BlogPosting/Breadcrumb JSON-LD, SiteSEO Organization fix, security hardening, legacy redirects (PHP fallback), contact form `[tbt_contact_form]`, removal of unused front-end assets |
| `wp-content/themes/twentytwentyfive/functions.php` | Removed duplicated SEO engine, inline 35 KB CSS duplicate, `time()` cache-buster, version stripping |
| `…/templates/page.html`, `single.html` | Page H1; visible "Updated" date on posts |
| `…/patterns/header.php`, `footer.php` | 7 KB WebP logo with dimensions (was 323 KB PNG), honest footer copy (no fake newsletter/"verified standards"), heading order, sitemap link |
| `…/assets/css/topblogtech-premium.css` | Page title, contact form, responsive tables, footer wrapping |
| `.htaccess` | Sensitive-file denylist, uploads script block, HTTPS redirect, single-hop absolute 301s, HTML cache headers, Permissions-Policy, 7 new slug redirects |
| `.gitignore` + untracked files | `wp-config.php` and 4 dumps/archives untracked |
| `tools/content-remediation/` (new, web-blocked) | `remediate.php` (dry-run/apply/rollback), `lib.php`, `validate-edits.php`, `edits/*.json` (33 files, 928 edits) |
| `wp-content/uploads/topblogtech-nav-logo-{132h,108h}.{png,webp}` (new) | Resized logos |
| `audit-reports/` | These reports; previous reports archived |

## 5. Remaining risks (be realistic)

1. **Low-value / unoriginal content judgement** — the main risk. The guides are now accurate summaries of public documentation; they still lack first-hand experience. Add real screenshots, your own numbers, worked examples to the strongest posts; consider merging overlapping money-making posts (7, 19, 20, 28).
2. **Saturated YMYL topics** (income, investing, crypto) — get extra scrutiny.
3. **Site age / re-crawl** — Google must re-crawl the fixed pages; the broken versions may be what it has cached.
4. **Credential exposure** — until rotated, anyone can use the public `wp-config.php` values if the DB host accepts remote connections.
5. **Theme version lock** (`99.9.9`) — keeps Twenty Twenty-Five from receiving security updates. Follow-up: move templates/CSS into a child theme, restore the real parent theme.
6. **Email deliverability** — until MX/SPF exist, `contact@` mail fails and form emails may land in spam.

## 6. Test results

| Test | Result |
|---|---|
| PHP lint — mu-plugin, functions.php, header.php, footer.php, 3 tool scripts | **PASS** |
| WordPress core checksums (local) / plugin checksums | **PASS** / **PASS** |
| Malware pattern scan (code + DB) | **PASS** |
| Editorial edits validator (33 files, 928 edits: unique match, block balance, idempotency) | **PASS** |
| Remediation rehearsal on fresh DB copy: dry-run → apply → re-run (0 changes) → rollback → re-apply (identical) | **PASS** |
| Rendered head tags on 35 URLs (1 robots/canonical/description/og:title; JSON-LD parses) | **PASS** |
| Visible `\n`, broken images, horizontal overflow, exactly one visible H1 — 35 URLs × 390/1366 px (70) and × 390/820/1366 px (105) | **PASS** |
| `.htaccess`: 19 sensitive paths blocked, assets 200, http→https, single-hop 301s | **PASS** (local Apache 2.4) |
| Contact form: nonce, validation, honeypot, timing, rate-limit | **PASS** |
| Contact form email delivery on production | **NOT TESTED** (no mail server locally; see deploy §8) |
| PHP debug log during all renders | **PASS** (no warnings from changed code) |
| Lighthouse / PageSpeed scores | **NOT TESTED** (tool unavailable; measured instead: article HTML 130 KB → 79 KB; render-path CSS/JS files 8 → 2; logo 323 KB → 7.4 KB) |
| Google Rich Results Test, URL Inspection, GSC reports | **BLOCKED** (needs live deploy + your GSC access) |
| Production behaviour (Cloudflare + cPanel) | **NOT TESTED** — run deploy §5 and §8 checks |
| Git secret check on staged changes | see commit notes in final summary |

## 7. Before you click "Request review"

1. Deploy code + run the content script (CPANEL_DEPLOYMENT §3–§8).
2. Move dumps out of web root, rotate DB password/salts/admin password, make repo private (§0, §9).
3. Add MX/SPF/DKIM/DMARC; send a test email to contact@ and a test via the contact form (§11, §8).
4. Confirm founder facts (About + author boxes) and the AI-use statement (Editorial Policy).
5. Submit `sitemaps.xml`, inspect 5–8 key URLs, request indexing; wait until the live-test screenshots show the fixed pages.
6. Add first-hand material to your best 8–10 articles.
7. In AdSense: add the site, paste the verification snippet, enable the EEA/UK/CH consent message, request review. After approval, create `ads.txt` with the exact line AdSense gives you.

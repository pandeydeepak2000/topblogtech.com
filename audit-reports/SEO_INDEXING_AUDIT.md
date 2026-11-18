# SEO & Indexing Audit — topblogtech.com

**Audit date:** 2026-11-18. **Method:** public crawl of the live site (curl, REST API, sitemaps, robots.txt) + local rendering of the remediated site on Apache/PHP against a copy of the production database.
**Google Search Console data:** **not available** to the auditor. Everything below is verified from public responses; items marked *GSC* need your export or URL Inspection.

## 1. Inventory (live, via REST API)

- **26 published posts**, **7 published pages** (Privacy, About, Terms, Affiliate Disclosure, Contact, Editorial Policy, Corrections Policy). 1 trashed page (sample-page, 301 to home).
- 18 categories (7 in use; **11 empty legacy categories** that already 301-redirect), 28 tags (each with 1–2 posts).
- Sitemap index: `https://topblogtech.com/sitemaps.xml` (SiteSEO) → post, page, category sitemaps. `/sitemap.xml` and `/wp-sitemap.xml` 301 to it.

## 2. Findings

| # | Sev. | Finding (evidence) | Root cause | Fix | Validation |
|---|---|---|---|---|---|
| I1 | **Critical** | Every article body showed literal `\n` text (306 occurrences on one article) — 9,506 sequences across all 33 posts/pages. Pages look broken to users and reviewers. | Content was written to MySQL double-escaped by earlier SQL-dump edits. | DB script stage 1 unescapes `\n`, `\'`, `\"` (no code blocks contain real backslashes — verified). | Local render of all 33 URLs: 0 visible `\n` — **PASS** |
| I2 | **Critical** | 78 `<img>` in articles (3 per post) pointed to `placeholder-*.webp` files that do not exist → 78 broken images (HTTP 404). | Placeholder images inserted but never uploaded. | DB script removes those 78 image blocks (no images invented). | 0 broken images on 105 page renders — **PASS** |
| I3 | High | 7 internal links pointed to wrong slugs → 404 (e.g. `/best-ai-seo-tools-to-rank-on-google/`). | Slugs typed by hand. | Links rewritten to real slugs (DB) + 301s for the wrong slugs (`.htaccess` + PHP fallback). | 0 broken internal links; 301s tested — **PASS** |
| I4 | High | Duplicate head tags on every page: 2× `robots`, 2× `canonical`, 2× `description`, 2× OG/Twitter sets, conflicting `twitter:card`. | Theme "SEO Engine" duplicated SiteSEO output. | SiteSEO is now the single owner of title/description/canonical/robots/OG; theme duplicate removed. | 35 URLs: exactly 1 robots / 1 canonical / 1 description / 1 og:title — **PASS** |
| I5 | High | Meta descriptions contained `YouTube\\\'s` (escaped garbage); 1 SiteSEO description was garbled ("unit economicsging…"). | Same escaping defect in excerpts/meta. | New clean 140–160-char excerpts for all 33 items; SiteSEO per-post descriptions synced. | Rendered — **PASS** |
| I6 | High | `http://topblogtech.com/` returned **200** (no HTTPS redirect) → duplicate http/https URLs. | No redirect at origin; Cloudflare "Always Use HTTPS" off. | Loop-safe 301 to https in `.htaccess`. Also enable Cloudflare "Always Use HTTPS". | `X-Forwarded-Proto: http` → 301 https — **PASS** (local); live after deploy |
| I7 | High | Category sitemap listed **11 URLs that 301** (empty legacy categories) → "Page with redirect" in GSC. | Categories consolidated but terms not deleted. | DB script deletes the 11 empty categories (count verified 0 at runtime); default category → Blogging; 301s kept. | Rehearsal: 11 deleted, rollback recreates — **PASS** |
| I8 | Medium | 6 of 7 pages had **no H1** (template had no title; CSS also hid titles). 1 page had H1 inside content. | `page.html` lacked a title block; `.page .wp-block-post-title {display:none}`. | Title H1 added to `page.html`; CSS exempts it; content H1 → H2. | Exactly 1 *visible* H1 on 35 URLs at 390 px & 1366 px — **PASS** |
| I9 | Medium | Homepage title was just "TopBlogTech"; description "Learn \| Grow \| Earn" (printed twice). | SiteSEO home settings defaults. | Descriptive home title + description set in SiteSEO option (DB script). | Rendered — **PASS** |
| I10 | Medium | 28 tag archives with 1–2 posts each are indexable thin pages. | — | SiteSEO `noindex` for tag archives (not in sitemap already). | Option set in rehearsal — **PASS** |
| I11 | Medium | Structured data: SiteSEO Organization with **empty `name`**; theme BlogPosting with `dateModified` earlier than `datePublished` on several items (hand-edited dates); WebSite `SearchAction` (retired feature). | Two systems, manual date edits. | SiteSEO Organization filled (name, logo, publishingPrinciples); single `@graph` BlogPosting + BreadcrumbList from mu-plugin; dateModified never < datePublished; post_modified set to real edit time. | All JSON-LD parses; types: Organization + BlogPosting + BreadcrumbList on posts — **PASS** (Rich Results Test: **NOT TESTED**, needs live URL) |
| I12 | Medium | HTML had `Expires: +1 month` → browsers keep stale pages after fixes. | `ExpiresDefault` applied to HTML. | HTML/XML/JSON now `0 seconds`. | Header present locally — **PASS** |
| I13 | Medium | Mobile: all pages scrolled sideways at 390 px (footer link row and comparison tables). | No wrapping/overflow handling. | CSS: footer wraps; tables scroll in their own box. | 105 renders (390/820/1366 px): 0 horizontal overflow — **PASS** |
| I14 | Low | Footer "Sitemap" link → `/wp-sitemap.xml` (redirect). | — | Points to `/sitemaps.xml`. | **PASS** |
| I15 | Low | Comment feeds advertised although comments not used. | — | Removed. | **PASS** |
| I16 | Info | robots.txt: Cloudflare-managed AI-crawler blocks + origin rules; `Googlebot` and `Mediapartners-Google` (AdSense) are **allowed**; sitemap line correct. | — | No change needed. | **PASS** |
| I17 | Info | Slugs ending in `-modern` and slugs containing "tested"/"speed-tests"/"fast" no longer match the corrected content. | Earlier renames. | **Kept** to avoid another round of redirects (titles corrected instead). | — |

Not issues: author/date/search archives already `noindex` (SiteSEO); attachment pages redirect to parent; `www` → apex 301; `?p=ID` → canonical 301; pagination canonical OK; 404s return real 404 with `noindex`.

## 3. Likely GSC statuses and what to expect

| GSC status | Probable cause found | After deployment |
|---|---|---|
| Page with redirect | 11 category URLs in sitemap, `/…-2024/` legacy slugs, http→https | Category URLs leave the sitemap; legacy 301s are correct — leave them |
| Crawled/Discovered – currently not indexed | Pages visibly broken (I1, I2) and largely unsourced/generic content | Re-request indexing after content fix; indexing is Google's decision |
| Duplicate without user-selected canonical | Possible http/https duplicates (I6) | Resolved by 301 |
| Not found (404) | 7 wrong internal slugs | Now 301 |
| Excluded by noindex | Tag/author/date/search archives | Intended |

## 4. Search Console steps (after deployment)

1. **Sitemaps** → remove any old `wp-sitemap.xml` entry → submit `https://topblogtech.com/sitemaps.xml`.
2. **URL Inspection** → Test live URL for: home, `/about-us/`, `/contact-us/`, `/privacy-policy/`, and 3–5 articles (e.g. `/getting-google-adsense-approval-fast/`, `/best-password-managers-1password-bitwarden-dashlane-modern/`). Check "Page fetch: Successful", rendered screenshot shows no `\n`, then **Request indexing** (a handful per day; don't spam).
3. **Pages report** → for "Page with redirect" / "Not found (404)" groups click **Validate fix**.
4. **Rich Results Test** (`search.google.com/test/rich-results`) on one article — expect Article/BlogPosting + Breadcrumb, no errors.
5. Export Pages + Performance reports in 2–4 weeks if you want a follow-up audit based on real GSC data.

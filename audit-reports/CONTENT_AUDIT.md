# Content Audit — topblogtech.com

**Audit date:** 2026-11-18 · **Inventory:** 26 posts + 7 pages (live REST API; matches the repo database copy word-for-word within ±15 words per item).
**Method:** every item was read in full and fact-checked; every number, price, policy statement and external link that was kept was verified against the live official source (WebFetch, Nov 2026). Detailed per-item tables: [`content-findings/<ID>.md`](content-findings/).

> **Status of these changes: PREPARED + TESTED, NOT YET LIVE.** Content lives in MySQL, so pushing to GitHub does not change the site. The changes are applied on production by `tools/content-remediation/remediate.php` (dry-run by default, JSON backup, rollback) — see `CPANEL_DEPLOYMENT.md`.

## 1. Totals

| Metric | Count |
|---|---|
| Items reviewed | 33 (26 posts, 7 pages) |
| Logged content findings | 242 in 30 detailed tables + edit logs for posts 60/62/64 |
| Reviewed editorial edits (exact find/replace) | **928**, all validated: apply exactly once, block markup balanced, idempotent |
| Mechanical fixes on every item | literal `\n`/`\'` unescaped (9,506), 78 broken placeholder images removed, 7 wrong internal links fixed |
| Titles changed | 3 (posts 7, 19, 40 — removed "Evidence-Based"/"That Actually Make Money"/"tested" claims) |
| Slugs changed | 0 (URLs and rankings preserved) |
| New excerpts / meta descriptions | 33 |

## 2. Systemic issues found (site-wide)

| Sev. | Issue | Examples | Action |
|---|---|---|---|
| Critical | **Fabricated statistics** presented as fact | "AdSense rejects 80–90% of applications" (16); CPC dollar ranges per niche (34); RPM/income figures (6, 7, 19, 32); TTFB "speed test" numbers (38); 15+ invented salaries (46); dividend projection table inflated to $1.14M (58) | Removed, replaced with official sourced figures (e.g. BLS May 2025 medians, Google AdSense Help wording) or qualitative wording |
| Critical | **Fake first-hand testing / benchmarks** | "our benchmarks", "hands-on benchmarks", SWE-bench "Clear Leader" ranking (56), "tested" tools (40), VPN speed claims (64) | Removed; each comparison now states that no hands-on tests were run and that it is based on vendor documentation and published sources |
| Critical | **Fictitious organisation** | "TopBlogTech Editorial Team… web developers, security analysts", "Research Desk", "Standards Desk", "four-stage peer review", "editorial offices" on posts and policy pages; **a fabricated US mailing address (Sheridan, WY)** and claimed affiliate programs (Amazon, NordVPN, hosts) on the Affiliate Disclosure page | Replaced with the truth: one-person site run by the founder; no affiliate links exist (verified: zero affiliate/tracking URLs on the site) |
| High | **Outdated facts / prices** | Bitwarden Premium "$10/yr" (now $19.80); Claude 3.5 Sonnet as current; Copilot Workspace; FID instead of INP; GA4 "IP anonymisation" step; Lemon Squeezy (no new sign-ups, closing 2027-03-31 per its own pricing page); PHP 8.2 advice; T+1 settlement | Updated with sources, or replaced exact prices with links to official pricing pages |
| High | **Wrong or misattributed sources** | Pew credited for YouTube stats it doesn't publish (14); 2016 BLS article misquoted (7); SBA credited with advice it doesn't give (52); Investor.gov credited with tickers/expense ratios (44); REIT 90% rule attributed to securities law (19) | Corrected attribution or removed |
| High | **Dead source links** | web.dev/explore/metrics, CFTC trading-programs advisory, IRS passive-activity page, Stripe resource, FINRA/Investor.gov dividend pages, consumer.ftc.gov Wi-Fi page, SBA page; `example.com` as a live link (50) | Replaced with current official URLs (fetched) or unlinked |
| High | **YMYL risk wording** (finance, crypto, income, security) | Guaranteed/"strictly capped" safety claims on crypto bots (48); no "not financial advice" note (19, 44, 58) | Added concise risk/adviser notes; removed guarantees; added CFTC "AI won't turn trading bots into money machines" advisory |
| Medium | **Legal pages inaccurate** | Privacy Policy claimed an IAB TCF CMP was running; Terms referenced non-existent services, a venue and addresses; contact page embedded a form that did not exist and showed "form may not work" | Rewritten to match real data flows (GA4 + Consent Mode, Cloudflare, Loginizer, email-only contact form) |
| Medium | Generic AI filler and hype anchors ("Make It Profitable Fast", "First $1,000") | Many posts | Trimmed; link text de-hyped |
| Low | Headings/tables | H3 used as top level on Privacy; missing header rows | Fixed where found |

## 3. Per-item summary

| ID | Item | Edits | Highest-severity issues fixed |
|---|---|---|---|
| 3 | Privacy Policy | 3 (rewrite) | False CMP claim; added GA4/Consent Mode, Cloudflare, Loginizer, contact form; last-updated date |
| 6 | Start a Tech & Finance Blog | 27 | Invented RPM/affiliate/traffic figures; outdated WP requirements; GA4 IP step |
| 7 | Realistic Ways to Earn Online | 21 | Income projections; misquoted BLS; tax rule → IRS guidance; title |
| 14 | YouTube Algorithm | 30 | Stats misattributed to Pew; invented CTR benchmarks → YouTube's own figure |
| 16 | AdSense Approval Checklist | 39 | "80–90% rejected"; review-time misquote; invented article/word/traffic minimums |
| 19 | Passive Income Ideas | 39 | Fake analyst team; return/income figures; dead IRS link; title |
| 20 | Multiple Income Streams | 30 | Unsourced income/percentage claims |
| 22 | About | 6 | "Verified against real-world testing" → research-based; one-person disclosure |
| 23 | Terms of Service | 21 | Non-existent services/programs, venue and address claims |
| 24 | Affiliate Disclosure | 1 (rewrite) | **Fabricated WY address**, fake programs, fake team, H1 in content |
| 25 | Contact | 12 | Broken form shortcode + "may not work" note; fake desks/offices |
| 27 | Grow on X | 32 | Outdated X features/monetisation claims |
| 28 | AI Tools to Make Money | 50 | Prices/versions; income claims; testing claims |
| 30 | Affiliate Marketing for Beginners | 25 | FTC guidance links; income claims |
| 32 | Faceless YouTube Channel | 26 | YouTube "inauthentic content" policy; RPM claims |
| 34 | Highest CPC Niches | 31 | All CPC dollar ranges; viewability/Smart Pricing claims |
| 36 | AI Automation Agency | 16 | Fee/retainer ranges; outdated app counts |
| 38 | WordPress Hosting | 35 | Fake TTFB tests; wrong prices/specs |
| 40 | AI SEO Tools | 30 | Fake testing; stale prices; title |
| 42 | Sell Digital Products | 20 | Lemon Squeezy wind-down; fees |
| 44 | HYSA vs Index Funds | 24 | Unsourced returns/APYs; FDIC wording; tax table |
| 46 | High-Income Skills | 28 | 15+ invented salaries → BLS medians |
| 48 | Crypto Trading Bots | 21 | Guarantees; dead CFTC link; risk scenarios |
| 50 | Technical SEO / CWV | 22 | Thresholds; INP replaced FID; example.com link |
| 52 | Micro-SaaS No-Code | 22 | Misattributed SBA advice; launch costs |
| 54 | Paid Newsletter | 25 | CAN-SPAM misstatement; platform fees verified |
| 56 | AI Coding Assistants | 56 | Outdated models/prices/features; benchmark ranking |
| 58 | Dividend Growth Investing | 63 | Inflated projection table rebuilt; dead links; IRS Pub 550 |
| 60 | Password Managers | 24 | Outdated prices ($10/yr Bitwarden etc.); unverified VPN bundle; "our benchmarks"; fake team box |
| 62 | n8n vs Make vs Zapier | 44 | Pricing/plan limits; licensing wording |
| 64 | VPN Comparison | 53 | Audit history corrected to sources; unverified prices/server counts; speed claims |
| 66 | Editorial Policy | 29 | Fake multi-stage review → realistic one-person process; AI-use statement |
| 67 | Corrections Policy | 23 | Fake desk, 48-h guarantee → corrections log via email |

## 4. Needs owner input (cannot be verified by the auditor)

1. **Founder facts** on About/author boxes (software developer, India, building since 2019) — kept as provided in the previous commit; confirm they are accurate.
2. **AI-use statement** on the Editorial Policy — confirm it matches how articles are actually produced (the content shows strong signs of AI drafting; Google allows AI-assisted content when it is accurate and useful, and transparency helps).
3. **Corrections log** promised on the Corrections Policy — keep it up to date.
4. **Terms venue** — say whether a specific Indian city should be named for jurisdiction.
5. **Data retention** — confirm GA4 retention (Admin → Data retention) and Loginizer log retention match the Privacy Policy.
6. **Time-sensitive posts** (56 AI coding, 28 AI tools, 40 AI SEO tools, 60 password managers, 64 VPN, 62 automation) — review quarterly.
7. Post 40: Outranking's site was down (HTTP 521) during review — replace/drop that tool if it stays offline.
8. Post 46 title "No Degree Required" is borderline; left unchanged.

## 5. Remaining content risk (honest assessment)

The articles are now accurate and much less misleading, but they remain **research summaries without first-hand experience, original data, screenshots or examples from the author's own work**. Many topics (make money online, AdSense, passive income, crypto bots) are heavily saturated. For Google's "helpful content"/AdSense "low value content" judgement, the biggest remaining lever is adding genuine first-hand material (your own setup screenshots, real numbers from this site, worked examples) to the strongest 8–10 posts, and pruning or merging the weakest overlapping money-making posts (7, 19, 20, 28 overlap heavily).

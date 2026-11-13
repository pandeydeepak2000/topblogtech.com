# WEBSITE OWNER ACTION GUIDE — TOPBLOGTECH.COM

**Target Website:** `https://topblogtech.com/`  
**Date:** November 13, 2026  
**Purpose:** Step-by-step instructions for the website owner to deploy the remediated theme code, import the enhanced database, and complete final verification steps for Google AdSense readiness.

---

## ⚡ QUICK DEPLOYMENT OVERVIEW

To push all improvements live to `topblogtech.com`, follow these 2 primary steps:
1. **Pull the updated theme code from GitHub**
2. **Import the remediated database in cPanel MySQL**

---

## STEP 1: PULL LATEST THEME CODE ON CPANEL SERVER

Open your **cPanel Terminal** (or SSH into your server) and execute:

```bash
cd /home/earningin/topblogtech.com
git fetch origin main
git reset --hard origin/main
```

> **What this does:** Updates your theme styles, eliminates all header margin gaps, fixes mobile navigation, and activates inlined critical CSS.

---

## STEP 2: IMPORT THE ENHANCED DATABASE

All 26 articles, 5 core pages, 2 new editorial policy pages, and metadata fixes are compiled inside `topblogtech_live_database.sql`.

### Option A: Via cPanel Terminal (Fastest - 10 Seconds)
Run this command in your cPanel Terminal (replace `YOUR_DB_PASSWORD` with your database password found in `wp-config.php`):

```bash
mysql -u earningin_wp703 -p earningin_wp703 < /home/earningin/topblogtech.com/topblogtech_live_database.sql
```
*(Enter your database password when prompted)*

### Option B: Via phpMyAdmin (GUI)
1. Log into your **cPanel**.
2. Click on **phpMyAdmin** under the *Databases* section.
3. In the left sidebar, click on the database: `earningin_wp703`.
4. Click on the **Import** tab at the top.
5. Click **Choose File** and select `topblogtech_live_database.sql`.
6. Scroll down and click **Import** (or **Go**).
7. Confirm that a green checkmark appears indicating success.

---

## STEP 3: VERIFY CONTACT EMAIL INBOX

Google AdSense policy requires a working, monitored communication channel.

1. Go to **cPanel > Email Accounts**.
2. Verify that **`contact@topblogtech.com`** exists.
   * If not created, click **+ Create**, set username as `contact`, and choose a secure password.
3. Send a test email from your personal Gmail/Yahoo account to `contact@topblogtech.com`.
4. Open **cPanel Webmail** (or configure email forwarding to your personal email) and confirm that the test message was received.

---

## STEP 4: GOOGLE SEARCH CONSOLE & SITEMAP

1. Log into [Google Search Console](https://search.google.com/search-console).
2. Select property `https://topblogtech.com/`.
3. Go to **Sitemaps** on the left menu.
4. Add new sitemap: `wp-sitemap.xml` and click **Submit**.
5. Confirm that Google reports **Status: Success** and discovers all posts and pages.
6. Check **Pages > Indexed** to ensure search engines have crawled your articles.

---

## STEP 5: COOKIE CONSENT BANNER (FOR EEA / UK TRAFFIC)

If any of your site visitors come from the European Economic Area (EEA), United Kingdom, or Switzerland:
1. Google requires a **Google-certified CMP (Consent Management Platform)** supporting IAB TCF 2.2 before serving ads.
2. Go to **WordPress Admin > Plugins > Add New**.
3. Search for and install **Complianz | GDPR/CCPA Cookie Consent** or **Site Kit by Google**.
4. Run the setup wizard to display a compliant cookie notice.

---

## STEP 6: SUBMIT SITE FOR GOOGLE ADSENSE REVIEW

Once Steps 1 through 5 are complete:
1. Log into your [Google AdSense](https://www.google.com/adsense/) dashboard.
2. Go to **Sites** > Click **Add Site**.
3. Enter `topblogtech.com` and click **Save**.
4. Copy the AdSense verification code snippet (or use Site Kit by Google to auto-link).
5. Ensure your verification code is in place, then click **Request Review**.
6. Google review typically takes between **2 to 14 business days**.

---

## 📞 NEED ASSISTANCE?
All remediated files, audit logs, and SQL backups are preserved locally in the repository. If you encounter any error during the import, refer to `ADSENSE_AUDIT.md` and `ADSENSE_READINESS.md` for full implementation details.


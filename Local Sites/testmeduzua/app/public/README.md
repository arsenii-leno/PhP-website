---

# MED.UZ.UA — WordPress Refactoring

> Local development and refactoring workspace for the MED.UZ.UA website.

## Project Goal

The objective of this repository is **not** to rewrite the website from scratch.

Instead, we are performing a systematic refactoring while preserving compatibility with WordPress, the existing database, and the production environment.

---

## Technical Audit & Current Architecture

### 1. Global Project Metrics (Post-Cleanup)

* **Initial Directory Size:** ~2.4 GB (Cleaned down to 800 MB locally).
* **Database state:** Active MySQL, integration with WooCommerce.
* **Primary Tech Stack:** WordPress, PHP, MySQL, WooCommerce, Betheme, WP Rocket, Wordfence, Google Tag Manager (GTM), Google Analytics 4 (GA4).

### 2. Detailed Classification & Risk Matrix

| Directory / File | Category | Modifier Risk | Purpose & Refactoring Action |
| --- | --- | --- | --- |
| `wp-admin/` | 🔴 Never modify | **Extreme** | WordPress Core admin interface. **Ignored in Git.** |
| `wp-includes/` | 🔴 Never modify | **Extreme** | WordPress Core libraries. **Ignored in Git.** |
| `wp-content/themes/betheme/` | 🔴 Never modify | **High** | Parent premium theme. **Ignored in Git.** Used only as code reference. |
| `wp-content/plugins/` | 🔴 Never modify | **High** | Third-party plugins. **Tracked in Git.** Read-only code. Modifiable only via hooks. |
| `wp-content/uploads/` | 🔴 Never modify | **None** | User-uploaded media. **Ignored in Git.** |
| `wp-content/cache/` | 🔴 Never modify | **None** | Cache directory (WP Rocket). **Ignored in Git.** Safe to empty/purge. |
| `wp-content/updraft/` | 🔴 Never modify | **None** | Backup archives (UpdraftPlus). **Ignored in Git.** Safe to empty/purge. |
| `wp-config.php` | 🟡 Modify carefully | **High** | Core configuration & DB credentials. **Tracked in Git.** |
| `wp-content/mu-plugins/` | 🟡 Modify carefully | **High** | Must-use plugins (loaded automatically). **Tracked in Git.** |
| `wp-content/themes/betheme-child/functions.php` | 🟡 Modify carefully | **High** | Custom PHP entry point (currently ~1 KB). **Primary Refactoring Target.** |
| `wp-content/themes/betheme-child/style.css` | 🟢 Safe to modify | **Low** | Custom CSS stylesheets. **Tracked in Git.** |

---

## Active Refactoring Decisions

### 1. Git Strategy

The repository is strictly **PRIVATE** due to sensitive configuration data (`wp-config.php`), premium themes/plugins licenses, and custom business logic.

* **Active branch:** `refactor/plugin-cleanup` (for cleanups and refactoring).
* **Production branch:** `master` / `main`.

### 2. Theme Architecture

* **Parent Theme:** Betheme (Premium Multi-purpose theme).
* **Child Theme:** `betheme-child` (Active). All custom theme modifications must reside *only* in this directory.
* *Note:* ~564 MB of old theme `.zip` archives (`betheme-22.0.zip`, `betheme_old.zip`) have been permanently deleted from `wp-content/themes/` to clean the workspace.

### 3. Plugin Cleanup Strategy (36 Plugins Found)

During the audit, we identified heavy bloating and feature duplication. The following decisions have been made:

#### A. Analytics & Tracking (GTM/GA4 consolidation)

* **Keep:** `duracelltomi-google-tag-manager` (Google Tag Manager for WordPress).
* **Remove:** `google-analytics-for-wordpress` (MonsterInsights - too heavy, duplicates GTM) and `header-and-footer-scripts` (custom scripts will be consolidated).
* **Action:** Migrate all tracking pixels (GA4, Facebook, Ads) strictly into the GTM container.

#### B. Redundant Protocol Helpers

* **Remove:** `http-https-remover` and `https-redirection`.
* **Action:** Replace these plugins by forcing HTTPS at the server level (Fornex/cPanel, `.htaccess` rewrite rules, or `wp-config.php` constants).

#### C. Database & Resource Optimization

* **Remove:** `wp-statistics` (writes massive log tables directly to local MySQL database, causing DB bloat). Use GA4 instead.
* **Remove:** `backupbuddy` (legacy backup solution, duplicates active `updraftplus` plugin).
* **Remove:** `woo-gutenberg-products-block` (obsolete, features now natively integrated into WooCommerce core).

#### D. Page Builders & Editors

* **Investigation Required:** Analyze if pages are built using Betheme's native Muffin Builder or WPBakery Page Builder (`js_composer` + `Ultimate_VC_Addons`). If WPBakery is unused, remove it.

---

## Workflow

```
Production (Fornex)
        ↓
Local copy (IntelliJ IDEA)
        ↓
Git (Private Repo / refactor branch)
        ↓
Step-by-step Refactoring & Testing
        ↓
Clean ZIP Package generation (excluding cache, uploads, backups)
        ↓
cPanel File Manager Deployment to Fornex

```

---

## Author

Local refactoring project for MED.UZ.UA.


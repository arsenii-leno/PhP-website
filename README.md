# 🏥 Legacy Medical Infrastructure: Rescue, Stabilization & Migration Bridge

> **Production Context:** Core architecture, diagnostic remediation, and data stabilization of the legacy WordPress monolith for [med.uz.ua](https://med.uz.ua) (Ophthalmology & Aesthetic Medical Center).

---

## 📌 Executive Summary

This repository documents the end-to-end engineering rescue of a high-traffic production medical platform. The system suffered from plugin bloat, cache inode exhaustion, legacy dependencies (WPBakery/Revolution Slider), and security vulnerabilities.

Through targeted PHP refactoring, custom `mu-plugins` implementation, and data-layer instrumentation, the platform was stabilized, secured, and prepared for its subsequent migration to an Edge-rendered Astro architecture.

---

## 🔍 System Architecture & Technical Debt Audit

| Vector                   | Legacy State                                      | Remediation & Engineering Intervention                                                        |
| :----------------------- | :------------------------------------------------ | :-------------------------------------------------------------------------------------------- |
| **Rendering Engine**     | Visual Composer (`js_composer`) + Bloated Sliders | Decommissioned resource-heavy plugins; neutralized `revslider-off` security surface.          |
| **Cache & Disk I/O**     | 1,200+ unpruned `wp-super-cache` static fragments | Reconfigured cache lifetime, cleared file locks, prevented inode depletion on Fornex hosting. |
| **Core Logic**           | Fragmented plugin dependencies                    | Isolated critical business flows into dedicated `wp-content/mu-plugins/med-clinic-core.php`.  |
| **Commerce & Logistics** | Unmonitored checkout funnels                      | Native integration of `wc-liqpay`, `wc-ukr-shipping`, and custom error telemetry.             |
| **Analytics & Tags**     | Broken manual tracking tags                       | Clean injection of `GTM4WP` with Enhanced Conversions and GA4 event schema.                   |

---

## 🛠️ Key Engineering Modules & Remediation

### 1. Custom Core Logic Isolation (`mu-plugins`)

To decouple medical booking rules and clinic workflow from unstable themes, business-critical logic was refactored into a persistent **Must-Use Plugin**:

* **Path:** `wp-content/mu-plugins/med-clinic-core.php`
* **Purpose:** Ensures continuous uptime, executes regardless of theme changes, prevents accidental client deactivation, and optimizes database query loops.

### 2. Threat Vector Neutralization & Malware Remediation

* Performed deep static analysis across all PHP headers (`eval`, `base64_decode`, obfuscated payloads).
* Hardened `wp-config.php` database connection strings, salts, and file permission policies (`chmod 644/755`).
* Isolated vulnerable slider modules (`revslider-off`) and sanitized active plugins (`antispam-bee`, `wps-hide-login`).

### 3. Measurement & Conversion Infrastructure

* Structured data-layer orchestration via Google Tag Manager (`duracelltomi-google-tag-manager`).
* Implemented conversion triggers for direct phone calls (`call-now-button`), appointment submissions, and service requests.
* Fed clean signal data into automated Google Ads AI campaigns.

---

## 🚀 The Migration Bridge to Edge Architecture

Stabilizing this monolithic instance provided the clean data baseline and traffic insights required to design the next-generation platform:

```text
[Legacy Monolith (WP + PHP + MySQL)]
               │
               ▼
(Data Extraction & Architecture Modernization)
               │
               ▼
[Production Edge Architecture (Astro + Tailwind + Cloudflare Pages)]
               │
               ├── ⚡ Sub-100ms LCP & 100/100 Core Web Vitals
               ├── 🛡️ Zero-JS Attack Surface & Serverless Form Processing
               └── 📈 Direct Smart Bidding (Value-Based Google Ads)
```

---

## 💻 CLI Diagnostic & Maintenance Commands

```bash
# Verify Database Connection
php -r "require 'wp-config.php'; global \$wpdb; echo \$wpdb->check_connection() ? 'OK' : 'FAIL';"

# Static analysis for eval/base64 patterns
grep -rnE "(eval\(|base64_decode\()" wp-content/plugins/ --exclude-dir=vendor

# Clear stale supercache static buffers
rm -rf wp-content/cache/supercache/med.uz.ua/*
```

---

## 👨‍💻 Engineering Ownership

* **Lead Engineer:** Arsenii Leno (Software Engineering @ FIIT STU Bratislava | Law & IP @ UzhNU)
* **Domain:** Systems Stabilization, Full-Stack Architecture & Conversion Engineering

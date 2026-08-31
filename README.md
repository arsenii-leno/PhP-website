# 🏥 WordPress Legacy Engineering: Recovery, Stabilization & Modernization

[![Status](https://img.shields.io/badge/Status-Stabilized%20%26%20Archived-amber?style=for-the-badge\&logo=git)](https://med.uz.ua)
[![WordPress](https://img.shields.io/badge/CMS-WordPress%20Core-21759B?style=for-the-badge\&logo=wordpress)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4.x%20%E2%86%92%208.2.x-777BB4?style=for-the-badge\&logo=php)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge\&logo=mysql)](https://www.mysql.com)
[![WooCommerce](https://img.shields.io/badge/Commerce-WooCommerce%20%2B%20LiqPay-96588A?style=for-the-badge\&logo=woocommerce)](https://woocommerce.com)
[![Hosting](https://img.shields.io/badge/Hosting-cPanel%20%2F%20Fornex-FF6C2C?style=for-the-badge\&logo=cpanel)](https://fornex.com)
[![Analytics](https://img.shields.io/badge/Measurement-GTM4WP%20%2F%20GA4-246FDB?style=for-the-badge\&logo=googletagmanager)](https://tagmanager.google.com)

> **Production Case Study:** Recovery, runtime modernization, and engineering stabilization of the legacy WordPress infrastructure behind [med.uz.ua](https://med.uz.ua) — an Ophthalmology & Aesthetic Medical Center platform.

---

## ⚡ Engineering Highlights

| Achievement                   | Result                                                                                               |
| :---------------------------- | :--------------------------------------------------------------------------------------------------- |
| **PHP Runtime Migration**     | Direct **PHP 7.4.x → PHP 8.2.x** migration while preserving the existing WordPress architecture      |
| **Legacy WordPress Recovery** | Stabilized a mature production installation with accumulated plugin, theme, and builder dependencies |
| **Architecture Preservation** | Existing content model, WPBakery layouts, WooCommerce flows, and business logic remained operational |
| **Cache Recovery**            | Diagnosed and remediated 1,200+ stale `wp-super-cache` fragments and filesystem pressure             |
| **Security Remediation**      | Audited PHP execution surfaces, isolated vulnerable legacy components, and hardened configuration    |
| **Business Logic Isolation**  | Moved critical application logic into a persistent WordPress Must-Use Plugin layer                   |
| **Commerce Stabilization**    | Preserved WooCommerce, LiqPay, shipping, checkout, and telemetry integrations                        |
| **Measurement Recovery**      | Consolidated GTM, GA4, Enhanced Conversions, phone-call, form, and commerce signals                  |
| **Modernization Bridge**      | Prepared the stabilized legacy system for migration toward an Astro + Cloudflare Edge architecture   |

---

## 🎯 Engineering Scope

This project demonstrates hands-on engineering work with a **mature, heavily customized production WordPress environment**/

The system combined:

* years of accumulated technical debt
* Muffin & WPBakery-generated layouts(chaotic mix)
* shortcode and plugin dependencies
* Revolution Slider components
* legacy PHP execution paths
* WooCommerce payment and logistics integrations
* filesystem-heavy caching
* security-sensitive legacy modules
* custom business logic
* production analytics and conversion tracking

> **Security Incident / Recovery Note:** The legacy WordPress dependency surface — including outdated plugins, shortcode-driven components, and abandoned frontend modules — had already been exploited, resulting in a compromised installation with malicious PHP payloads and trojanized files. The platform was successfully recovered through static inspection, malicious-code removal, vulnerable component isolation, configuration hardening, and validation of critical production workflows, while preserving the existing site architecture and content.


The objective was **not to immediately rebuild the website from scratch**.

The challenge was to understand an interconnected legacy application, identify its failure domains, preserve business-critical behavior, remove or isolate dangerous components, modernize the runtime, and bring the platform back to a **stable and maintainable baseline**.

```text
WordPress Core
      │
      ├── PHP Runtime
      ├── MySQL
      ├── Themes
      ├── WPBakery / Visual Composer
      ├── Plugin Ecosystem
      ├── WooCommerce
      ├── Cache / Filesystem
      ├── Security
      └── Analytics / Conversion Layer
```

This required treating WordPress not merely as a CMS, but as a **legacy PHP application platform with real production dependencies**.

---

# 🚑 Recovery Phase

## 1. Legacy Installation Diagnostics

Before changing the application, the production environment was mapped and inspected as an interconnected system.

Diagnostics covered:

* WordPress Core and configuration
* active and inactive plugin dependencies
* theme and child-theme behavior
* PHP runtime compatibility
* MySQL connectivity
* cache filesystem state
* WooCommerce integrations
* page-builder dependencies
* suspicious PHP execution patterns
* forms and customer-facing workflows
* analytics and conversion flows

The objective was to classify components into four practical categories:

```text
┌───────────────────────┐
│ Business-Critical     │ → Preserve & Validate
├───────────────────────┤
│ Required Legacy       │ → Stabilize / Contain
├───────────────────────┤
│ Replaceable           │ → Refactor / Decouple
├───────────────────────┤
│ Redundant / Dangerous │ → Disable / Remove / Isolate
└───────────────────────┘
```

This dependency-first approach reduced the risk of "fixing" one component while silently breaking another production workflow.

---

## 2. WPBakery / Visual Composer Recovery

The frontend relied heavily on **Visual Composer / WPBakery (`js_composer`)** and builder-generated content structures.

Instead of treating generated builder code as an untouchable black box, the recovery process involved understanding its place within the wider WordPress dependency graph.

Engineering work included:

* analysis of builder-generated page structures
* shortcode dependency identification
* preservation of existing content
* identification of plugin-dependent UI elements
* reduction of unnecessary runtime overhead
* separation of custom application logic from presentation logic
* maintenance of existing pages during backend remediation

The important constraint was:

> **Modernize the runtime and infrastructure without requiring the clinic to reconstruct years of existing content.**

This is particularly relevant when recovering WordPress installations where page builders effectively form part of the application's persisted data model.

---

## 3. Plugin Dependency Recovery

The installation contained interconnected plugins responsible for rendering, caching, security, commerce, payments, shipping, analytics, and conversions.

Examples included:

* `js_composer`
* Revolution Slider / `revslider-off`
* `wp-super-cache`
* `duracelltomi-google-tag-manager`
* `call-now-button`
* `antispam-bee`
* `wps-hide-login`
* `wc-liqpay`
* `wc-ukr-shipping`

The problem was therefore not simply:

```text
"Which plugins are old?"
```

but:

```text
"Which runtime dependencies can be modified or removed
without breaking an unrelated production workflow?"
```

Critical execution paths were mapped before components were disabled, replaced, or isolated.

This allowed unnecessary attack surface and overhead to be reduced while preserving functionality required by the clinic.

---

# ⚡ PHP Runtime Modernization

## PHP 7.4.x → PHP 8.2.x

> ### Major Engineering Milestone
>
> The production application was migrated directly from **PHP 7.4.x to PHP 8.2.x while preserving the established WordPress architecture, content model, WPBakery layouts, plugin ecosystem, WooCommerce integrations, and business-critical workflows.**

This was intentionally **not** an architectural rewrite.

The existing application had to survive a substantial runtime-generation jump while retaining its established behavior.

### Compatibility Surface

The migration required validation and remediation across:

| Layer              | Compatibility Concern                                    |
| :----------------- | :------------------------------------------------------- |
| **WordPress Core** | Runtime compatibility and execution behavior             |
| **Custom PHP**     | Deprecated behavior, warnings, notices, and fatal errors |
| **WPBakery**       | Builder and shortcode execution                          |
| **Legacy Plugins** | Compatibility with PHP 8.x semantics                     |
| **WooCommerce**    | Checkout and commerce execution paths                    |
| **LiqPay**         | Payment integration behavior                             |
| **Shipping**       | `wc-ukr-shipping` integration                            |
| **Caching**        | Filesystem and cache-generation behavior                 |
| **Forms**          | Appointment and service-request workflows                |
| **Analytics**      | GTM / GA4 conversion signals                             |

### Migration Path

```text
┌──────────────────────────────┐
│          PHP 7.4.x           │
│     Legacy Production        │
└──────────────┬───────────────┘
               │
               ▼
      Compatibility Audit
               │
               ▼
      Dependency Mapping
               │
               ▼
      Legacy Code Remediation
               │
               ▼
      Plugin Compatibility
               │
               ▼
      Runtime Error Diagnostics
               │
               ▼
      Critical Workflow Testing
               │
               ▼
┌──────────────────────────────┐
│          PHP 8.2.x           │
│    Modernized Production     │
└──────────────────────────────┘
               │
               ├── WordPress architecture preserved
               ├── Content model preserved
               ├── WPBakery layouts preserved
               ├── WooCommerce preserved
               ├── Payment / shipping preserved
               └── Business workflows preserved
```

### Why This Matters

A PHP major-version upgrade in a mature WordPress installation is not equivalent to changing a version selector in cPanel.

The runtime sits underneath:

```text
Theme
  ↓
Builder
  ↓
Shortcodes
  ↓
Plugins
  ↓
WooCommerce
  ↓
Custom PHP
  ↓
WordPress Core
  ↓
PHP Runtime
```

A compatibility failure at any layer can propagate upward into broken pages, checkout failures, missing forms, PHP fatal errors, or inaccessible administration.

The result was therefore a runtime modernization **without forcing the business to absorb the cost and risk of an immediate architectural rewrite**.

---

# 🧠 Custom WordPress / PHP Engineering

## Must-Use Plugin Architecture

Business-critical functionality was moved away from fragile theme/plugin coupling into a dedicated WordPress **Must-Use Plugin**:

```text
wp-content/
└── mu-plugins/
    └── med-clinic-core.php
```

The MU-plugin layer provides a persistent execution location for application-specific functionality that should not disappear when:

* a theme is replaced
* a normal plugin is accidentally disabled
* builder components change
* frontend templates are refactored

Conceptually:

```text
┌───────────────────────────────────────┐
│          Presentation Layer           │
│ Themes / Templates / WPBakery         │
└──────────────────┬────────────────────┘
                   │
                   ▼
┌───────────────────────────────────────┐
│       WordPress Application Layer     │
│ Plugins / WooCommerce / Integrations  │
└──────────────────┬────────────────────┘
                   │
                   ▼
┌───────────────────────────────────────┐
│       med-clinic-core.php             │
│ Persistent Business-Critical Logic    │
└───────────────────────────────────────┘
```

This reduced the coupling between **clinic-specific business behavior** and the site's presentation layer.

---

# ⚡ Cache, Filesystem & Hosting Recovery

The production problems extended below WordPress itself.

The installation had accumulated more than **1,200 stale `wp-super-cache` static fragments**.

On constrained hosting, uncontrolled cache growth can become a filesystem and inode-management problem rather than a performance optimization.

Recovery included:

* cache directory inspection
* stale static buffer cleanup
* cache lifetime reconfiguration
* file-lock cleanup
* filesystem usage monitoring
* inode pressure mitigation
* validation of cache regeneration behavior

Example maintenance operation:

```bash
rm -rf wp-content/cache/supercache/med.uz.ua/*
```

The objective was not merely to perform a one-time purge.

The goal was to restore a **predictable cache lifecycle compatible with the hosting environment**.

---

# 🛡️ Security & Malware-Oriented Remediation

Legacy WordPress installations expose a large PHP execution surface, including code that may no longer be reachable from the visible frontend but still exists on disk.

Static inspection included suspicious and frequently abused PHP constructs:

```bash
grep -rnE "(eval\(|base64_decode\()" wp-content/plugins/ --exclude-dir=vendor
```

> Pattern detection alone does not prove malicious behavior. Matches were treated as investigation targets requiring contextual inspection.

The remediation process covered:

* inspection of PHP files for obfuscated payloads
* investigation of suspicious `eval` usage
* investigation of `base64_decode` patterns
* legacy plugin review
* isolation of vulnerable slider components
* active/inactive plugin inspection
* `wp-config.php` hardening
* database connection configuration review
* WordPress salt verification
* filesystem permission normalization
* `644/755` permission-policy review
* unnecessary attack-surface reduction

Components such as `revslider-off` were treated as security-sensitive code surfaces rather than assumed safe merely because they were not part of the visible frontend.

---

# 🛒 WooCommerce & Business-Critical Integrations

The platform contained commerce and logistics functionality that had to remain operational throughout remediation.

Production integrations included:

* WooCommerce checkout flows
* `wc-liqpay` payment processing
* `wc-ukr-shipping`
* checkout error telemetry
* service-request tracking
* conversion measurement

This introduced an important engineering constraint:

> **Infrastructure stabilization could not come at the expense of customer-facing or revenue-related workflows.**

The recovery process therefore treated checkout, payments, shipping, and conversion events as critical application paths requiring validation after infrastructure changes.

---

# 📊 Analytics & Conversion Engineering

The legacy measurement implementation was consolidated around Google Tag Manager through `duracelltomi-google-tag-manager`.

Tracked interactions included:

* direct phone calls
* `call-now-button` interactions
* appointment submissions
* service requests
* WooCommerce actions
* conversion events

The resulting measurement layer fed:

* **Google Tag Manager**
* **Google Analytics 4**
* **Enhanced Conversions**
* **Google Ads AI-powered campaigns**

Conceptually:

```text
WordPress / WooCommerce
          │
          ├── Calls
          ├── Forms
          ├── Appointments
          ├── Service Requests
          └── Commerce Events
                    │
                    ▼
              Data Layer
                    │
                    ▼
            Google Tag Manager
                    │
             ┌──────┴──────┐
             ▼             ▼
            GA4       Google Ads
                       Enhanced
                       Conversions
```

The stabilized application therefore became a cleaner source of behavioral and conversion signals rather than merely a recovered website.

---

# 🔧 Production Diagnostics & CLI Maintenance

Legacy WordPress recovery frequently requires operating below the WordPress Admin layer.

## Database Connection Verification

```bash
php -r "require 'wp-config.php'; global \$wpdb; echo \$wpdb->check_connection() ? 'OK' : 'FAIL';"
```

## Suspicious PHP Pattern Scan

```bash
grep -rnE "(eval\(|base64_decode\()" wp-content/plugins/ --exclude-dir=vendor
```

## Super Cache Cleanup

```bash
rm -rf wp-content/cache/supercache/med.uz.ua/*
```

The workflow combines:

```text
WordPress Admin
      +
PHP Runtime Diagnostics
      +
Filesystem Inspection
      +
MySQL Validation
      +
Hosting-Level Maintenance
```

This is particularly important when WordPress itself is too degraded to provide reliable diagnostics through the administrative interface.

---

# 🔬 Recovery Methodology

The project followed a conservative legacy-system recovery philosophy:

```text
        OBSERVE
           │
           ▼
         AUDIT
           │
           ▼
     MAP DEPENDENCIES
           │
           ▼
    IDENTIFY FAILURE
        DOMAINS
           │
           ▼
        ISOLATE
           │
           ▼
       REMEDIATE
           │
           ▼
       VALIDATE
           │
           ▼
       STABILIZE
           │
           ▼
       MODERNIZE
```

The principle is simple:

> **Do not rewrite a system before understanding what the existing system is actually doing.**

For legacy WordPress installations, apparently obsolete components may still own shortcodes, persisted content, checkout hooks, tracking events, AJAX handlers, scheduled actions, or other hidden dependencies.

Recovery therefore prioritizes **dependency awareness and controlled intervention over destructive cleanup**.

---

# 🚑 Legacy WordPress Recovery Capability

This case represents WordPress engineering where the starting point is not:

```text
New WordPress
+ New Theme
+ Clean Database
+ Modern Plugins
```

but instead:

```text
Years of Production History
            │
            ├── Legacy PHP
            ├── WPBakery
            ├── Shortcodes
            ├── Old Plugins
            ├── WooCommerce
            ├── Payments
            ├── Shipping
            ├── Cache Problems
            ├── Filesystem Constraints
            ├── Security Concerns
            ├── Analytics
            └── Existing Traffic
```

The engineering path becomes:

```text
[Unstable Legacy WordPress]
            │
            ├── Core / PHP / MySQL Diagnostics
            ├── Plugin Dependency Mapping
            ├── WPBakery / Builder Analysis
            ├── PHP 7.4 → 8.2 Compatibility Work
            ├── Cache & Filesystem Recovery
            ├── Security Inspection
            ├── WooCommerce Stabilization
            ├── Analytics Recovery
            └── MU-Plugin Logic Isolation
            │
            ▼
[Controlled Legacy Architecture]
            │
            ├── Stable
            ├── Understandable
            ├── Maintainable
            └── Migration-Ready
```

The practical capability demonstrated by this project is therefore:

> **Taking a heavily modified WordPress installation — including builders, legacy plugins, custom PHP, WooCommerce, hosting constraints, and accumulated technical debt — and recovering it into an understandable and operational system before deciding what should be maintained, refactored, or replaced.**

---

# 🚀 Migration Bridge to Modern Architecture

The recovered WordPress system also provided the technical and data baseline required for the next generation of the platform.

```text
┌──────────────────────────────┐
│       Legacy Monolith        │
│   WordPress + PHP + MySQL    │
└──────────────┬───────────────┘
               │
        Recovery & Audit
               │
               ▼
┌──────────────────────────────┐
│   Stabilized WordPress       │
│      PHP 8.2 Baseline        │
└──────────────┬───────────────┘
               │
               ├── Data Extraction
               ├── Content Mapping
               ├── Traffic Analysis
               ├── Conversion Validation
               └── Architecture Planning
               │
               ▼
┌──────────────────────────────┐
│    Modern Edge Platform      │
│ Astro + Tailwind + Cloudflare│
└──────────────────────────────┘
               │
               ├── ⚡ Sub-100ms LCP
               ├── 🎯 100/100 Core Web Vitals
               ├── 🛡️ Reduced JS Attack Surface
               ├── ☁️ Serverless Form Processing
               └── 📈 Value-Based Ads Signals
```

The legacy platform was therefore not simply discarded.

It was:

**diagnosed → understood → recovered → secured → runtime-modernized → stabilized → instrumented → prepared for migration**

This transformed the old WordPress installation from an opaque liability into a controlled migration source.

---

# 🧰 Demonstrated Engineering Skills

### WordPress Engineering

* Legacy WordPress diagnostics and recovery
* WordPress Core configuration
* custom PHP development
* Must-Use Plugins (`mu-plugins`)
* hooks and application-level logic isolation
* theme/plugin dependency analysis
* production troubleshooting
* legacy architecture preservation

### PHP Modernization

* PHP 7.4.x → PHP 8.2.x migration
* runtime compatibility analysis
* deprecated behavior remediation
* fatal-error diagnostics
* third-party plugin compatibility investigation
* legacy PHP modernization
* production workflow validation

### Page Builders & Legacy Frontend

* WPBakery / Visual Composer
* shortcode-based layouts
* Revolution Slider
* builder-generated content structures
* legacy frontend dependency remediation
* content preservation during infrastructure changes

### Performance & Infrastructure

* `wp-super-cache`
* cache lifecycle management
* filesystem diagnostics
* inode troubleshooting
* PHP runtime diagnostics
* MySQL connectivity
* cPanel / Fornex hosting
* hosting-level maintenance

### Security

* suspicious PHP pattern analysis
* malware-oriented static inspection
* legacy plugin isolation
* `wp-config.php` hardening
* filesystem permissions
* attack-surface reduction
* security-sensitive dependency review

### WooCommerce

* checkout-flow diagnostics
* LiqPay integration
* Ukrainian shipping integrations
* commerce telemetry
* conversion tracking
* business-critical workflow preservation

### Analytics & Growth Infrastructure

* Google Tag Manager
* GTM4WP
* Google Analytics 4
* Enhanced Conversions
* Google Ads conversion signals
* phone-call tracking
* appointment tracking
* service-request tracking
* commerce events

### Systems Thinking

* legacy-system recovery
* dependency mapping
* failure-domain analysis
* incremental remediation
* architecture preservation
* runtime modernization
* migration preparation
* business-continuity-oriented engineering

---

# 👨‍💻 Architecture & Engineering

**Arsenii Leno**
*Double-degree Student in Software Engineering (FIIT STU Bratislava) & Law (UzhNU Faculty of Law)*
*Google Certified AI-Powered Performance Ads Specialist*

* 🌐 **Portfolio & Case Studies:** [arsenii-leno.github.io](https://arsenii-leno.github.io)
* 📑 **Workfolio Hub:** [Notion Workspace](https://bouncy-pyroraptor-569.notion.site/Workfolio-16c46a8dd0cd80f28fd6c43b2b604b21)
* 💬 **Telegram:** [@Arsen_Kozaque](https://t.me/Arsen_Kozaque)
* ✉️ **Email:** [arsenii.leno.digital@gmail.com](mailto:arsenii.leno.digital@gmail.com)

---

> **Repository Scope:** This repository documents architecture, recovery methodology, diagnostics, and engineering decisions. Proprietary medical data, production credentials, customer information, and protected application assets are intentionally excluded.

All proprietary code, medical catalogs, branding, and assets are protected.
Copyright © 2026 Arsenii Leno. All rights reserved.

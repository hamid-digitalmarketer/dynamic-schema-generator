<h1 align="center">Dynamic Schema Generator for WordPress</h1>
<h3 align="center">Automated, Production-Grade JSON-LD Schema.org Structured Data Engine with Elementor Integration</h3>

<p align="center">
  <a href="https://github.com/hamid-digitalmarketer/dynamic-schema-generator/actions"><img src="https://img.shields.io/badge/CI-Passing-2ea44f?style=for-the-badge&logo=githubactions&logoColor=white" alt="CI Status"></a>
  <a href="https://wordpress.org/"><img src="https://img.shields.io/badge/WordPress-5.8%20to%206.7+-21759b?style=for-the-badge&logo=wordpress&logoColor=white" alt="WordPress Compatibility"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-7.4%20|%208.0%20|%208.1%20|%208.2%20|%208.3-777bb4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Versions"></a>
  <a href="https://schema.org/"><img src="https://img.shields.io/badge/Data%20Format-JSON--LD%20@graph-f59e0b?style=for-the-badge" alt="JSON-LD Format"></a>
  <a href="https://wa.me/923167417135?text=Hello%20Hamid%2C%20I%20saw%20your%20Dynamic%20Schema%20Generator%20plugin%20on%20GitHub%20and%20would%20like%20to%20discuss%20a%20project%20or%20job%20opportunity."><img src="https://img.shields.io/badge/WhatsApp-Contact%20Author-25d366?style=for-the-badge&logo=whatsapp&logoColor=white" alt="WhatsApp Consultation"></a>
</p>

---

## 📌 Executive Overview

**Dynamic Schema Generator** (`dynamic-schema-generator`) is an enterprise-grade, object-oriented WordPress plugin engineered by **Muhammad Hamid** (Digital Marketing Head & MarTech Engineer). It automatically generates accurate, interconnected **Schema.org JSON-LD** structured data directly from WordPress content, landing pages, and **Elementor** layouts.

### The Problem It Solves
1. **AI-Generated Content Saturation:** Search engines (Google, Bing) and AI answer models (Gemini, ChatGPT, Perplexity) struggle to separate authentic business entities from synthetic spam. Structured, linked Knowledge Graphs enable crawlers to unambiguously verify who you are, what services you provide, and what questions you answer.
2. **Zero-Click Searches:** Rich snippets and AI Overviews summarize answers directly in SERPs. Structured data provides verified entity anchors without hallucinations.
3. **Bloatware in Legacy SEO Plugins:** Unlike bulky SEO suites that inject render-blocking scripts, heavy CSS, or vendor ads, Dynamic Schema Generator focuses exclusively on high-precision structured data with zero frontend runtime overhead.

> **Important SEO Reality Check:** Valid structured data improves machine readability and eligibility for search engine features. Search engines decide independently how and when to display rich snippets or cite pages in AI Overviews. Dynamic Schema Generator never promises guaranteed rankings or rich snippet placements.

---

## ⚡ Core Features

```
+-----------------------------------------------------------------------------------+
|                        Dynamic Schema Generator Engine                            |
+-----------------------------------------------------------------------------------+
            |                                           |
            v                                           v
[ Standard WordPress Content ]                 [ Elementor Layouts ]
  • Posts & Pages                              • Post Document Tree Traversal
  • Hierarchical Categories                    • Accordion Widgets -> FAQPage
  • Post Authors -> Person                     • Toggle Widgets    -> FAQPage
  • Featured Images -> ImageObject             • Heading Widgets   -> Section Outline
            |                                           |
            +---------------------+---------------------+
                                  |
                                  v
                    +---------------------------+
                    |  Schema.org Graph Builder |
                    +---------------------------+
                      • Organization / LocalBusiness (#organization)
                      • WebSite (#website) + SearchAction
                      • WebPage (#webpage) + BreadcrumbList (#breadcrumb)
                      • Article / BlogPosting (#article)
                      • Service (#service) + Offers & Pricing
                      • FAQPage (#faq)
                                  |
                                  v
                    +---------------------------+
                    |   In-House Validator      |
                    |   Syntax, @ids, Types     |
                    +---------------------------+
                                  |
                                  v
                    +---------------------------+
                    |   Clean JSON-LD Output    |
                    |   Injected into <head>    |
                    +---------------------------+
```

### 1. Interconnected Schema `@graph` Architecture
Generates a consolidated `@graph` array linking entities via persistent `#` URIs:
- **`Organization` / `LocalBusiness`:** Brand identity, legal name, telephone, email, physical address (`PostalAddress`), geographic coordinates (`GeoCoordinates`), opening hours (`OpeningHoursSpecification`), and authoritative social profiles (`sameAs`).
- **`WebSite`:** Site entity with `SearchAction` potentialAction for Google sitelinks search box.
- **`WebPage`:** Page entity linked to site hierarchy, authors, and canonical URLs.
- **`BreadcrumbList`:** Hierarchical navigation trail with numerical `ListItem` positions.
- **`Article` / `BlogPosting`:** Headline, datePublished, dateModified, author (`Person`), and featured image (`ImageObject`).
- **`Service`:** Mapped landing page service offerings, category, provider link, and explicit pricing (`Offer`).
- **`FAQPage`:** Question and answer pairs extracted from manual post configuration or Elementor widgets.

### 2. Dedicated Elementor Integration Layer
- **Non-Invasive Tree Traversal:** Directly reads `_elementor_data` document structures without DOM rendering penalties.
- **Automatic FAQ Extraction:** Converts `accordion` and `toggle` widgets into Schema.org `FAQPage` markup.
- **Heading Outline Inspection:** Extracts `heading` widgets for section context.
- **Zero-Dependency Fallback:** If Elementor is inactive or missing, the plugin operates smoothly on standard WordPress content without throwing errors.

### 3. Business Information & Local SEO Manager
Full management interface under **Dynamic Schema > Business Info**:
- 12 supported business types: `Organization`, `LocalBusiness`, `ProfessionalService`, `LegalService`, `MedicalBusiness`, `FinancialService`, `Store`, etc.
- Precision geographic latitude and longitude for Google Maps and Local 3-Pack proximity.
- Sanitized `sameAs` array for knowledge graph establishment across LinkedIn, X, GitHub, and corporate directories.

### 4. Service Schema Builder & Page Mapping
- Map specific services to dedicated landing pages.
- Optional, honest pricing: prices are only emitted when explicitly supplied by the administrator. Never fabricates estimates or pricing ranges.

### 5. In-House Schema Preview & Validator
- Select any published post or the global homepage to inspect the live generated JSON-LD.
- Real-time linting detects missing required properties, invalid `@context`, duplicate `@id`s, or malformed JSON syntax.
- One-click copy and direct links to the **Google Rich Results Test** and **Schema.org Validator**.

### 6. Conflict Safeguards & Diagnostics
- Detects existing active installations of **Yoast SEO**, **Rank Math**, **All in One SEO (AIOSEO)**, and **SEOPress**.
- Provides duplicate-output suppression and post-level exclusion checkboxes.

---

## 🗂️ Technical Directory Structure

```
dynamic-schema-generator/
├── dynamic-schema-generator.php     # Main bootstrap & autoloader
├── uninstall.php                    # Safe uninstaller with clean option
├── readme.txt                       # WordPress.org plugin directory specification
├── README.md                        # Comprehensive GitHub repository guide
├── LICENSE                          # MIT License
├── composer.json                    # PSR-4 autoloading & developer scripts
├── phpunit.xml.dist                 # PHPUnit test configuration
├── docker-compose.yml               # Local WordPress 6.6 + MySQL 8.0 demo
├── .gitignore                       # Clean Git exclusion rules
├── .github/
│   └── workflows/
│       └── ci.yml                   # CI Matrix across PHP 7.4 - 8.3
├── includes/
│   ├── class-plugin.php             # Lifecycle orchestrator
│   ├── class-activator.php          # Activation & default options
│   ├── class-deactivator.php        # Deactivation & transient cleanup
│   ├── class-settings.php           # Settings getter/setter & sanitization
│   ├── class-admin.php              # Admin screens, menus, AJAX & meta boxes
│   ├── class-business-manager.php   # Organization & LocalBusiness profile
│   ├── class-service-manager.php    # Service definitions & page mapping
│   ├── class-faq-manager.php        # FAQ management & deduplication
│   ├── class-schema-generator.php   # Core @graph entity generator
│   ├── class-schema-validator.php   # Syntax & Schema.org property linter
│   ├── class-schema-output.php      # wp_head output renderer
│   ├── class-diagnostics.php        # Health checks, telemetry & audit logs
│   └── integrations/
│       ├── interface-page-builder-adapter.php # Modular adapter interface
│       └── class-elementor-adapter.php        # Elementor JSON data parser
├── admin/
│   ├── css/admin-style.css          # Responsive WordPress admin styling
│   ├── js/admin-script.js           # AJAX preview, copy tool, log cleaner
│   └── views/                       # 9 modular admin view templates
├── languages/
│   └── dynamic-schema-generator.pot # Internationalization template
└── tests/
    ├── bootstrap.php                # WordPress mocking harness
    ├── run-tests.php                # Standalone test runner
    ├── test-settings.php
    ├── test-business-manager.php
    ├── test-service-manager.php
    ├── test-faq-manager.php
    ├── test-elementor-adapter.php
    ├── test-schema-generator.php
    └── test-schema-validation.php
```

---

## 🚀 Installation & Quick Start

### Method 1: WordPress Admin Upload (ZIP Archive)
1. Download `dynamic-schema-generator.zip` from the [Releases](https://github.com/hamid-digitalmarketer/dynamic-schema-generator/releases) tab.
2. In your WordPress dashboard, navigate to **Plugins > Add New > Upload Plugin**.
3. Select the `.zip` archive, click **Install Now**, and click **Activate Plugin**.
4. Open the **Dynamic Schema** menu to complete your Business Information.

### Method 2: Git Clone into Plugin Directory
```bash
cd wp-content/plugins/
git clone https://github.com/hamid-digitalmarketer/dynamic-schema-generator.git
```

### Method 3: Local Demonstration via Docker Compose
Run a complete, isolated WordPress 6.6 + MySQL 8.0 instance with the plugin auto-mounted:
```bash
docker compose up -d
```
Open [http://localhost:8080](http://localhost:8080) to complete the 1-minute WordPress setup and activate the plugin.

---

## 🧪 Automated Testing & Code Quality

The plugin includes an automated test suite covering settings sanitization, business entity validation, Elementor widget extraction, JSON-LD generation, and duplicate-id detection:

```bash
# Run standalone unit test runner (zero external dependencies required)
php tests/run-tests.php

# Run PHP syntax linting across all files
find . -name "*.php" -exec php -l {} \;
```

### GitHub Actions CI Workflow
Every push and pull request is automatically tested across a PHP version matrix (`7.4`, `8.0`, `8.1`, `8.2`, `8.3`) on Ubuntu runners:
- `composer validate --strict`
- Syntax linting (`php -l`)
- 20 Unit and Integration Tests (100% Passing)

---

## 🔧 Developer Filters & Extensibility

### 1. Conditionally Suppress Output
```php
add_filter( 'dsg_should_render_schema', function( $should_render ) {
    // Suppress schema output on custom checkout or private staging paths
    if ( is_page( 'private-portal' ) ) {
        return false;
    }
    return $should_render;
} );
```

### 2. Enrich the Schema Graph
```php
add_filter( 'dsg_schema_graph', function( $graph ) {
    // Append a custom event entity to the graph
    $graph['@graph'][] = array(
        '@type'     => 'Event',
        'name'      => 'Annual Performance Marketing Summit',
        'startDate' => '2026-11-15T09:00:00+00:00',
    );
    return $graph;
} );
```

---

## 👨‍💻 Author & Commercial Inquiries

**Muhammad Hamid**  
*Digital Marketing Head, Technical SEO Architect & MarTech Engineer*  
- 💬 **WhatsApp (Direct)**: [+92 316 7417135](https://wa.me/923167417135?text=Hello%20Hamid%2C%20I%20reviewed%20Dynamic%20Schema%20Generator%20and%20would%20like%20to%20discuss%20an%20enterprise%20SEO%20and%20Schema%20consulting%20project.)
- 🌐 **Portfolio & Case Studies**: [https://hamid-resume.web.app/](https://hamid-resume.web.app/)
- 🐙 **GitHub Profile**: [https://github.com/hamid-digitalmarketer](https://github.com/hamid-digitalmarketer)

---

## 📄 License
This project is open-source software licensed under the [MIT License](LICENSE).

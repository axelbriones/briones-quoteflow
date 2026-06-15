# Changelog

All notable changes to the Briones QuoteFlow plugin will be documented in this file.

## [1.0.0] - 2024-10-25

### Added
*   **Initial Release:** Core architecture for the Briones QuoteFlow plugin.
*   **B2B Replacement:** Automatically overrides WooCommerce "Add to Cart" and checkout functionalities.
*   **AJAX Modal:** Integrated responsive form capture.
*   **Variable Product Engine:** Dynamic detection and injection of chosen product attributes (Size, Color, etc.).
*   **Mini-CRM Dashboard:** Admin interface to manage leads, change statuses, and append timestamped Internal Notes.
*   **Timeline Tracker:** Automated event logging (Creation, Email dispatches, Status changes) attached to every lead.
*   **Reference ID Generator:** Custom auto-generated `BQF-XXXXXX` identifiers.
*   **Advanced Metrics:** Conversion Rate, Top Requested Products, and aggregated deal statuses in the dashboard.
*   **HTML Email Templates:** Responsive, visually structured email bodies for Admin and Customer notifications.
*   **Email Template Variables:** Dynamic replacement tags (`{product_name}`, `{customer_name}`, `{product_price}`).
*   **Email Diagnostics:** "Send Test Email" utility embedded into the settings.
*   **WhatsApp CRM Integration:** Click-to-chat `wa.me` links injected into the dashboard tables.
*   **Security & Spam Protection:** Honeypot field, Transients-based IP Rate Limiting, and Duplicate Lead detection.
*   **Data Exports:** 1-Click Print-to-PDF and Excel-compatible UTF-8 BOM CSV exporter.
*   **Visual Customizer:** Color-pickers embedded in Settings to style frontend buttons.
*   **HPOS Compatibility:** Explicitly declared `Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility`.
*   **Internationalization:** Implemented `.pot` base and `load_plugin_textdomain`.
*   **Uninstall Script:** Integrated `uninstall.php` standard file to remove options gracefully.
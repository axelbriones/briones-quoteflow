# Briones QuoteFlow

**Version:** 1.0.0
**Requires at least:** 5.8
**Tested up to:** 6.5
**Requires PHP:** 7.4+ (Optimized for 8.1+)
**Requires Plugin:** WooCommerce
**License:** GPLv2 or later

Briones QuoteFlow is an elegant, robust, and lightweight plugin designed to convert standard WooCommerce stores into powerful B2B Lead Generation platforms. It seamlessly replaces the conventional shopping cart with a highly customizable "Request a Quote" modal and introduces a built-in Mini-CRM directly within your WordPress dashboard.

## Features

### Frontend Experience
*   **Elegant Modal:** Instant, AJAX-driven pop-up modal replacing the "Add to Cart" button.
*   **Variable Product Support:** Automatically captures variations (e.g., Size, Color) chosen by the user before requesting a quote.
*   **Catalog Mode:** One-click toggle to completely disable the cart, mini-cart, and checkout functionalities store-wide.
*   **Visual Customization:** Easily adapt button colors and border-radius directly from the admin panel without touching CSS.
*   **Thank You Redirect:** Option to redirect the customer to a custom landing page upon successful submission.

### Backend Mini-CRM
*   **Dedicated Database Table:** Lightning-fast operations. Leads are securely saved in a dedicated `wp_bqf_quotes` table, completely isolated from `wp_posts`.
*   **Dashboard Metrics:** Visual cards tracking Total Leads, New Leads, Won/Lost Deals, and your overall Conversion Rate.
*   **Single Lead View:** Dedicated view for each lead acting as a mini-CRM.
*   **Timeline & Activity Logging:** Tracks when the lead was generated, when emails were dispatched, and every time the status changes.
*   **Internal Notes:** Add timestamped private notes to any quote request to keep your sales team aligned.
*   **WhatsApp Integration:** Single-click button next to the customer's phone number to instantly initiate a pre-filled WhatsApp conversation.
*   **PDF Generation:** Instantly generate clean, printable HTML/PDF records of the quote requests.
*   **CSV Exports:** Download your entire leads database perfectly formatted for Excel (UTF-8 BOM).

### Security & Professional Emails
*   **HTML Email Templates:** Beautiful, responsive HTML email templates for both the Admin notification and the Customer confirmation, complete with Product Images and Reference IDs.
*   **Intelligent Email Fallback:** If SMTP fails but the lead is saved to the CRM, the user gets a graceful "We are experiencing delays but your request is saved" notice.
*   **Robust Anti-Spam:** Integrated invisible honeypot and a 15-minute strict IP rate limit.
*   **Duplicate Detection:** Smartly rejects identical requests from the same user on the same day.

## Installation

1.  Upload the `briones-quoteflow` directory to your `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Ensure **WooCommerce** is installed and active.
4.  Navigate to the new **QuoteFlow** menu in the sidebar to adjust your settings, email templates, and visual preferences.

## Development & Architecture

Built with modern WordPress development standards:
*   Fully compatible with **WooCommerce HPOS** (High-Performance Order Storage).
*   Data architecture handles schema upgrades natively via `dbDelta()`.
*   Translation-ready with `.pot` files.
*   Secure AJAX routing and data sanitization using `$wpdb->prepare()`.
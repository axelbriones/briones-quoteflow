# Release Checklist v1.0.0

This checklist is used to perform Quality Assurance (QA) before formally freezing and releasing the plugin.

## Plugin Core
- [ ] Install plugin
- [ ] Activate plugin (verify `dbDelta` creates `wp_bqf_quotes`)
- [ ] Deactivate plugin
- [ ] Reinstall / Reactivate without generating fatal errors
- [ ] Uninstall (Verify `uninstall.php` triggers securely)

## WooCommerce Flow
- [ ] Request a Quote on a **Simple Product**
- [ ] Request a Quote on a **Variable Product** (ensure variations are captured)
- [ ] Verify "Add to Cart" and checkout are successfully disabled when "Catalog Mode" is active
- [ ] Verify "Product Out of Stock" state doesn't crash the modal
- [ ] Ensure plugin fails gracefully if WooCommerce is not installed

## Emails
- [ ] **Admin Notification:** Verify HTML template renders correctly (Product Image, Reference ID, Company, Price).
- [ ] **Customer Confirmation:** Verify HTML template renders correctly (Product Image, Reference ID, Price).
- [ ] **Test Email Button:** Test via `wp-admin > QuoteFlow > Settings` and verify success/error notices.
- [ ] Verify emails work when SMTP is active.
- [ ] Verify intelligent fallback notice appears to the customer if SMTP is disabled/failing.

## Mini-CRM (Admin Dashboard)
- [ ] New Quote Request appears in the Dashboard instantly.
- [ ] **Pagination** works (testing with > 20 leads).
- [ ] **Search** works (search by Name, Email, or Product).
- [ ] **Status Update:** Can change status (New, Contacted, Won, Lost, etc.) and it reflects in DB.
- [ ] **Internal Notes:** Can add a note, and it logs the timestamp and Admin name.
- [ ] **Timeline:** Verifying actions generate automatic timeline logs.
- [ ] **CSV Export:** Generated file has UTF-8 BOM, opens in Excel with correct encoding.
- [ ] **PDF Export:** Clean HTML view opens correctly and triggers `window.print()`.
- [ ] **WhatsApp Link:** Link is correctly formatted (`https://wa.me/...`) and opens a pre-filled message.
- [ ] **Delete Lead:** Removes the entry cleanly from the DB and reloads the view.

## Security & Anti-Spam
- [ ] AJAX actions strictly validate nonces (`wp_verify_nonce` / `check_ajax_referer`).
- [ ] Forms include a hidden **Honeypot**.
- [ ] **Duplicate Detection:** Prevents same email + same product + same day submission.
- [ ] **Rate Limiting:** Prevents more than 3 requests from the same IP within 15 minutes.
- [ ] Database queries utilize `$wpdb->prepare()`.

## Compatibility & QA
- [ ] Tested on **WordPress 6.x**
- [ ] Tested on **WooCommerce latest**
- [ ] Tested on **PHP 8.1 / 8.2**
- [ ] **HPOS (High-Performance Order Storage)** compatibility strictly declared and yields no warnings.
- [ ] Responsive Admin Dashboard (Metric boxes collapse cleanly on mobile/tablet).
- [ ] i18n ready (`.pot` file exists and `load_plugin_textdomain` is hooked).
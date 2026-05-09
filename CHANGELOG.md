# 📋 Changelog — KWL Maintenance Mode

All notable changes to this project are documented here.

---

## [2.1.8]

### 🔧 Fixed
- Admin CSS and JS moved from inline output to `wp_add_inline_style()` and `wp_add_inline_script()` via `admin_enqueue_scripts` hook
- `support_email` now sanitized with `sanitize_email()` instead of `wp_kses_post()`
- `facebook_url` and `custom_link_url` now sanitized with `esc_url_raw()` instead of `wp_kses_post()`

---

## [2.1.7]

### 🔧 Fixed
- Escaped all unescaped output in admin page (pill label, version constant, icon select labels)
- Replaced `parse_url()` with `wp_parse_url()` in front-end intercept
- Added nonce verification to `kwl_preview` GET parameter
- Removed Google Fonts CDN and Font Awesome CDN (offloaded content policy)
- Switched to inline SVG icons — no external dependencies, privacy-friendly, works offline
- Switched to system font stack — no external font loading
- Escaped inline CSS output via `wp_strip_all_tags()`
- Updated `readme.txt` Tested up to field to major version only (6.9)

---

## [2.1.6]

### 🔧 Fixed
- Contributors field updated to use wordpress.org username
- Added License URI to plugin header and readme
- Added Text Domain to plugin header
- All admin form `name` attributes now properly escaped with `esc_attr()`

---

## [2.1.5]

### ✨ Improved
- Robots meta tag is now set automatically based on the selected mode — Coming Soon always sends `index, follow`; Maintenance always sends `noindex, nofollow`. This prevents mismatched settings (e.g. Maintenance mode with `index, follow`). The manual robots dropdown has been replaced with an informational display showing exactly what each mode sends.

---

## [2.1.4]

### 🔧 Fixed
- Replaced broken inline SVG paths (which displayed wrong/deformed icons) with emoji-based icons in the icon circle. Emoji are universally supported, need no CDN, no SVG paths, and match the labels shown in the admin dropdown.

---

## [2.1.3]

### 🔧 Fixed
- Page icon definitively fixed. Replaced the CSS `mask-image` approach (which was unreliable across browsers) with direct inline SVG output from PHP. The icon SVG is now rendered server-side with the correct color baked in — no JavaScript, no CDN, no CSS tricks required.

---

## [2.1.2]

### 🔧 Fixed
- Page icon (tools, laptop, etc.) was showing blank on the maintenance page. The icon-mapping script was placed in `<head>` and ran before the page body existed, so `querySelectorAll` found no elements. Wrapped in `DOMContentLoaded`.

---

## [2.1.1]

### 🔧 Fixed
- Admin tab panels (General, Business Content, etc.) were all visible at once — missing CSS rule causing tabs to not hide/show correctly
- Page icon was not rendering — CSS still targeted old `<i>` tags from Font Awesome instead of the new self-hosted `<span>` elements
- Icon color CSS variable mismatch (`--icon` vs `--icon-color`) causing the icon to appear with no color

---

## [2.1.0]

### 🚀 New
- Three-way site mode selector — Off, Coming Soon (200 OK), Maintenance (503)
- Self-hosted icon set — no more Font Awesome CDN dependency (better privacy, GDPR-friendly, works offline)
- Coming Soon mode correctly sends `200 OK` so search engines can index the page
- Maintenance mode correctly sends `503` + `Retry-After` header
- Page title updates automatically to "Coming Soon" or "Under Construction" based on the selected mode
- Admin status pill now shows **OFF / COMING SOON / MAINTENANCE** clearly

---

## [2.0.3]

### 🔧 Fixed
- Portfolio template admin preview card was showing hardcoded `kenweill.com`, `Ken Weill`, and static subheading/button text. Preview card now reflects saved settings.

---

## [2.0.2]

### 🔧 Fixed
- `/wp-login.php` now correctly shows the maintenance page when Loginizer (or any login-rename plugin) is active. Previously it fell through to the theme's 404 handler instead.

---

## [2.0.1]

### 🔧 Fixed
- Added Author URI

---

## [2.0.0]

### 🚀 New
- Added Portfolio / Personal template
- Added custom link block (resume, project, etc.)
- Added per-template color customization
- Added icon selector
- Improved admin UI with template switcher cards

---

## [1.0.0]

### 🚀 New
- Initial release

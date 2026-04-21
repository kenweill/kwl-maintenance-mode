=== KWL Maintenance Mode ===
Contributors: Ken Weill
Tags: maintenance, under construction, coming soon
Requires at least: 6.0
Tested up to: 6.9.4
Stable tag: 2.1.0
License: GPL-2.0+

A fully customizable maintenance/under-construction page with two built-in templates.

== Description ==

KWL Maintenance Mode lets you display a beautiful, branded maintenance page to visitors
while your site is under construction — fully customizable from the WordPress admin.

Two templates included:
* Business / Brand — best for company or product sites
* Personal / Portfolio — adds a name badge, subheading, and a custom link/resume button

Three site modes:
* Off — site is live, plugin does nothing
* Coming Soon — returns 200 OK so search engines can index the page
* Maintenance — returns 503 + Retry-After so search engines know it's temporary

Customize everything:
* Company / site name and icon
* All text: tagline, ETA, status badge, footer
* Progress bar value and colors
* Contact links (email, Facebook)
* Custom link block (resume, project, portfolio, etc.)
* All colors independently per template
* Role-based bypass (admins, editors)
* SEO robots meta tag

No external icon CDN — icons are self-hosted inside the plugin for privacy compliance
and offline use.

== Installation ==

1. Upload the plugin ZIP via Plugins > Add New > Upload Plugin
2. Activate the plugin
3. Go to Settings > KWL Maintenance to configure
4. Pick a mode, pick a template, customize, done

== Changelog ==

= 2.1.0 =
* New: Three-way site mode selector — Off, Coming Soon (200 OK), Maintenance (503)
* New: Self-hosted icon set — no more Font Awesome CDN dependency
  (better privacy, GDPR-friendly, works offline)
* Coming Soon mode correctly sends 200 OK so search engines can index the page
* Maintenance mode correctly sends 503 + Retry-After header
* Page title updates automatically to "Coming Soon" or "Under Construction"
  based on the selected mode
* Admin status pill now shows OFF / COMING SOON / MAINTENANCE clearly

= 2.0.3 =
* Bug fix: Portfolio template admin preview card was showing hardcoded "kenweill.com",
  "Ken Weill", and static subheading/button text. Preview card now reflects saved settings.

= 2.0.2 =
* Bug fix: /wp-login.php now correctly shows the maintenance page when Loginizer
  (or any login-rename plugin) is active. Previously it fell through to the
  theme's 404 handler instead.

= 2.0.1 =
* Added Author URI

= 2.0.0 =
* Added Portfolio / Personal template
* Added custom link block (resume, project, etc.)
* Added per-template color customization
* Added icon selector
* Improved admin UI with template switcher cards

= 1.0.0 =
* Initial release

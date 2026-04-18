=== KWL Maintenance Mode ===
Contributors: Ken Weill
Tags: maintenance, under construction, coming soon
Requires at least: 6.0
Tested up to: 6.9.4
Stable tag: 2.0.3
License: GPL-2.0+

A fully customizable maintenance/under-construction page with two built-in templates.

== Description ==

KWL Maintenance Mode lets you display a beautiful, branded maintenance page to visitors
while your site is under construction — fully customizable from the WordPress admin.

Two templates included:
* Business / Brand — best for company or product sites
* Personal / Portfolio — adds a name badge, subheading, and a custom link/resume button

Customize everything:
* Company / site name and icon
* All text: tagline, ETA, status badge, footer
* Progress bar value and colors
* Contact links (email, Facebook)
* Custom link block (resume, project, portfolio, etc.)
* All colors independently per template
* Role-based bypass (admins, editors)
* SEO robots meta tag

== Installation ==

1. Upload the plugin ZIP via Plugins > Add New > Upload Plugin
2. Activate the plugin
3. Go to Settings > KWL Maintenance to configure
4. Pick a template, customize, then toggle maintenance mode ON

== Changelog ==

= 2.0.3 =
* Bug fix: Portfolio template admin preview card was showing hardcoded "kenweill.com",
  "Ken Weill", and static subheading/button text instead of the saved settings values.
  The preview card now reflects your actual saved site name, name badge, subheading,
  and custom link label.

= 2.0.2 =
* Bug fix: /wp-login.php now correctly shows the maintenance page when Loginizer
  (or any login-rename plugin) is active. Previously, the renamed login page caused
  wp-login.php to become a 404 that bypassed the maintenance intercept and showed
  the theme's 404 template instead.

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

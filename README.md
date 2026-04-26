# 🔧 KWL Maintenance Mode — WordPress Plugin

A free, fully customizable **maintenance / under-construction page** plugin for WordPress. No paywalls, no locked templates — everything is customizable right from your WordPress dashboard.

![WordPress](https://img.shields.io/badge/WordPress-6.9%2B-blue?logo=wordpress)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-purple?logo=php)
![License](https://img.shields.io/badge/License-GPL--2.0%2B-green)
![Version](https://img.shields.io/badge/Version-2.1.5-orange)

---

## ✨ Features

- 🚦 **One-click toggle** — turn maintenance mode on or off instantly
- 🎨 **Full color control** — customize background, card, title, buttons, and progress bar colors
- ✏️ **All text is editable** — site name, tagline, ETA message, status badge, footer, and more
- 📊 **Animated progress bar** — set your completion percentage visually with a slider
- 🔗 **Configurable contact links** — support email, Facebook page, and a "Check status" button
- 🔐 **Bypass by role** — admins (and optionally editors) always see the real site
- 👁️ **Live preview** — preview the maintenance page without enabling it for visitors
- 🤖 **SEO-friendly** — sends a proper `503` HTTP status and lets you set the robots meta tag
- 📱 **Fully responsive** — looks great on mobile, tablet, and desktop
- ⚡ **Lightweight** — single PHP file, no bloat, no external dependencies beyond Google Fonts + Font Awesome CDN

---

## 🖥️ Screenshots

### Maintenance Page (Visitor View)
> Clean, modern card design with animated progress bar, contact links, and your branding.

![KWL Maintenance Page](https://github.com/user-attachments/assets/be770fc4-9e88-420f-9a66-abd5774444d4)

### Admin Settings Panel
> Tabbed settings page with toggle switch, color pickers, range slider, and preview button.

---

## 📦 Installation

### Option A — Upload ZIP (recommended)

1. Download the latest release ZIP from the [Releases](../../releases) page
2. In your WordPress admin go to **Plugins → Add New → Upload Plugin**
3. Choose the ZIP file and click **Install Now**
4. Click **Activate Plugin**

### Option B — Manual

1. Clone or download this repo
2. Copy the `kwl-maintenance` folder into your site's `/wp-content/plugins/` directory
3. Activate from **Plugins** in your WordPress admin

---

## ⚙️ Configuration

After activating, go to **Settings → KWL Maintenance** in your WordPress admin.

### Tabs

| Tab | What you can customize |
|-----|------------------------|
| **General** | Maintenance mode toggle, site name, progress bar percentage |
| **Content & Text** | Tagline, ETA/status text, status badge, support note, footer text |
| **Colors** | Background gradient, card color, title gradient, body text, progress bar, button colors |
| **Contact Links** | Support email address, Facebook page URL, show/hide each link |
| **Access & SEO** | Role bypass (admins/editors), robots meta tag |

### Preview Without Going Live

Click the **👁 Preview Maintenance Page** button in the settings to see exactly what visitors will see — without enabling maintenance mode for everyone.

---

## 🔐 Who Can Bypass?

By default, logged-in **Administrators** always see the live site, not the maintenance page. You can also optionally allow **Editors** to bypass.

Everyone else — guests and lower-role users — will see the maintenance page when it's active.

---

## 🗂️ File Structure

```
kwl-maintenance/
├── kwl-maintenance.php   # Main plugin file (all logic + front-end template)
└── readme.txt            # WordPress.org readme format
```

---


## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. Fork the repo
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the **GPL-2.0+** license. See [`LICENSE`](LICENSE) for more information.

This license is required for WordPress plugins and means anyone can use, modify, and distribute this plugin freely — as long as they keep the same license.

---

## 👤 Author

**KWL Hub**
- Website: [kwlhub.com](https://kwlhub.com)
- Facebook: [@KWLHub](https://www.facebook.com/KWLHub)
- Email: support@kwlhub.com

---

> Built with ❤️ as a free alternative to paid maintenance page plugins. No locked templates, no upsells — just a clean, customizable page that works.

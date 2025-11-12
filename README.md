<div align="center">

# 📅 Vecco Timeline

[![Version](https://img.shields.io/badge/version-1.2.0-blue.svg)](https://github.com/wikiwyrhead/vecco-timeline/releases)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](LICENSE)

**A flexible horizontal timeline plugin for WordPress with draggable scroll, SVG icons, and beautiful responsive design.**

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Changelog](#-changelog) • [Support](#-support)

</div>

---

## ✨ Features

### 🎯 Core Functionality
- **Custom Post Type** - Manage multiple timelines with ease
- **Drag & Drop Reordering** - Intuitive timeline item organization
- **Flexible Icons** - Support for images, inline SVG, or 13 preset icons
- **Smart Styling** - Three-tier customization: Global → Per-timeline → Per-item
- **Smooth Scrolling** - Native drag-to-scroll with mouse wheel support and momentum
- **Responsive Design** - Optimized for desktop and mobile devices

### 🎨 Customization Options
- **Global Defaults** - Set site-wide accent color, icon size, typography
- **Per-timeline Overrides** - Custom fonts, colors, spacing for each timeline
- **Per-item Styling** - Individual icon colors, sizes, and typography
- **Typography Control** - Support for Google Fonts and custom web fonts
- **Responsive Spacing** - Different separator widths for desktop/mobile

### ⚡ Performance
- **Zero Dependencies** - No external libraries required
- **Lightweight** - Minimal CSS/JS footprint
- **On-demand Loading** - Assets loaded only when shortcode is used
- **Optimized Rendering** - Smooth performance even with many timeline items

---

## 📦 Installation

### Method 1: WordPress Admin (Recommended)
1. Download the latest release from [Releases](https://github.com/wikiwyrhead/vecco-timeline/releases)
2. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Choose the downloaded ZIP file and click **Install Now**
4. Click **Activate Plugin**

### Method 2: Manual Installation
```bash
cd wp-content/plugins/
git clone https://github.com/wikiwyrhead/vecco-timeline.git
```
Then activate via **WordPress Admin → Plugins**

### Method 3: FTP Upload
1. Download and extract the plugin
2. Upload `vecco-timeline` folder to `wp-content/plugins/`
3. Activate via **WordPress Admin → Plugins**

---

## 🚀 Usage

### Quick Start
1. Go to **WordPress Admin → Timelines → Add New**
2. Add your timeline items with years, titles, descriptions, and icons
3. Configure colors, fonts, and spacing in **Per-timeline Overrides**
4. Copy the timeline ID from the URL or post list
5. Add shortcode to any page or post:

```
[vecco_timeline id="123"]
```

### Global Settings
Navigate to **Timelines → Settings** to configure:

| Setting | Description |
|---------|-------------|
| **Accent Color** | Default color for icons and year text |
| **Icon Size** | Default icon dimensions (pixels) |
| **Base Font Size** | Default text size for timeline items |
| **Desktop Spacing** | Separator width on desktop (px) |
| **Mobile Spacing** | Separator width on mobile (px) |
| **Typography** | Font families for year, title, description |
| **Web Fonts** | Google Fonts or custom font URLs |
| **Mouse Wheel** | Enable/disable horizontal scroll |

### Per-timeline Overrides
Each timeline can override global settings:
- Base font size
- Separator color and width  
- Year/Title/Description colors, sizes, and fonts
- Custom web font URLs

### Per-item Customization
Each timeline item supports:
- **Icon Options**: Image URL, inline SVG, or preset icons
- **Accent Color**: Custom color per item
- **Icon Size**: Override default size
- **Typography**: Custom year color

---

## 🎨 Icon Presets

Choose from 13 built-in SVG icons:

| Icon | Use Case | Icon | Use Case |
|------|----------|------|----------|
| 🏗️ **Factory** | Manufacturing, Industrial | ⚡ **Bolt** | Energy, Innovation |
| 🛠️ **Wrench** | Maintenance, Engineering | 🏆 **Award** | Achievement, Milestone |
| 🧪 **Beaker** | Research, Science | 🚚 **Truck** | Logistics, Delivery |
| 🔌 **Plug** | Technology, Connection | 🌿 **Leaf** | Sustainability, Growth |
| 🪙 **Shovel** | Construction, Foundation | 🌍 **Globe** | Global, Expansion |
| 💼 **Briefcase** | Business, Corporate | 📊 **Chart** | Analytics, Growth |
| 👥 **Users** | Team, Community | | |

---

## 🛠️ Development

### File Structure
```
vecco-timeline/
├── assets/
│   ├── css/
│   │   └── timeline.css          # Frontend styles
│   └── js/
│       ├── timeline.js            # Drag scroll & custom scrollbar
│       └── vecco-swiper-init.js   # Legacy (unused)
├── includes/
│   ├── admin.php                  # Settings page & meta boxes
│   └── class-vecco-timeline.php   # Core CPT & rendering
├── vecco-timeline.php             # Main plugin file
├── readme.txt                     # WordPress.org format
└── README.md                      # This file
```

### Hooks & Filters
The plugin uses standard WordPress hooks:
- `init` - Register CPT and meta
- `wp_enqueue_scripts` - Load assets on-demand
- `add_meta_boxes` - Timeline editor interface

### Contributing
Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📋 Changelog

### 🎉 Version 1.2.0 - *November 2025*

#### 🎨 Enhanced Admin Interface
- ✨ Redesigned timeline editor with modern 4-column grid layout
- 🎴 Improved Timeline Settings page with card-based design
- 📚 Added comprehensive instructions explaining settings hierarchy (Global → Per-timeline → Per-item)
- 🎯 Enhanced visual grouping and field organization
- ♿ Increased font sizes and improved spacing for better accessibility
- 💡 Inline help text layout for better user guidance

#### 🐛 Bug Fixes
- 🖱️ Fixed mouse wheel horizontal scroll behavior on desktop
- 🎨 Fixed WordPress core CSS conflicts with scoped selectors
- 🔧 Removed duplicate field code in timeline editor

#### ⚡ Improvements
- 🎨 Visual design consistency across all admin pages
- 👁️ Better readability and user experience
- 📱 Improved responsive behavior

### 🚀 Version 1.0.0 - *Initial Release*
- 📝 Custom Post Type for timeline management
- 🎨 Shortcode rendering with customization options
- ⚙️ Admin settings and per-timeline overrides
- 🎯 Drag-and-drop item reordering
- 🖼️ SVG and image icon support
- 📱 Responsive horizontal timeline
- 🎢 Smooth inertial scrolling

---

## 💝 Support

### 🐛 Found a Bug?
Please [open an issue](https://github.com/wikiwyrhead/vecco-timeline/issues) with:
- WordPress version
- PHP version
- Steps to reproduce
- Expected vs actual behavior

### 💡 Feature Request?
We'd love to hear your ideas! [Open an issue](https://github.com/wikiwyrhead/vecco-timeline/issues) with the `enhancement` label.

### ☕ Donate
If this plugin helped your project, consider buying me a coffee:

[![Donate](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.paypal.me/arnelborresgo)

---

## 📄 License

This project is licensed under the **GPL-2.0-or-later** License - see the [LICENSE](LICENSE) file for details.

---

## 👤 Author

**arneLG** ([@wikiwyrhead](https://github.com/wikiwyrhead))

- GitHub: [github.com/wikiwyrhead](https://github.com/wikiwyrhead)
- PayPal: [paypal.me/arnelborresgo](https://www.paypal.me/arnelborresgo)

---

<div align="center">

**⭐ If you find this plugin useful, please star the repository! ⭐**

Made with ❤️ for the WordPress community

</div>

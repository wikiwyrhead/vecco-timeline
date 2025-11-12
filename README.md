# Vecco Timeline

**Version:** 1.2.0

A flexible horizontal timeline plugin for WordPress with draggable scroll, SVG and image icons, drag-and-drop reordering, per‑timeline styling, and a simple shortcode.

- Custom Post Type for managing multiple timelines
- Drag-and-drop reordering in the editor
- Image, SVG paste, or preset icons per item
- Per‑timeline style overrides: base font size, separator color/width, typography
- Global defaults in Settings
- Smooth drag and mouse-wheel scrolling with momentum
- Local assets and no third‑party dependencies by default

## Install
1. Copy `vecco-timeline` to `wp-content/plugins/`.
2. Activate in WP Admin > Plugins.
3. Go to Timelines > Add New to create a timeline.
4. Add to a page: `[vecco_timeline id="123"]`.

## Settings
- Accent Color, Icon Size, Base Font Size
- Separator Width (Desktop/Mobile)
- Fonts (Year/Title/Description) with presets
- Webfont URL(s) (global); per‑timeline override also supported

## Per‑timeline overrides
- Base font size, separator color
- Year/Title/Description: color, font size, font family (+ presets)
- Webfont URL(s)

## Development
- CPT and admin in `includes/`
- Assets in `assets/`
- Main plugin loader `vecco-timeline.php`

## Changelog

### Version 1.2.0
**Enhanced Admin Interface:**
- Redesigned timeline editor with modern 4-column grid layout
- Improved Timeline Settings page with card-based design
- Added comprehensive instructions explaining settings hierarchy (global → per-timeline → per-item)
- Enhanced visual grouping and field organization
- Increased font sizes and improved spacing for better accessibility
- Inline help text layout for better user guidance

**Bug Fixes:**
- Fixed mouse wheel horizontal scroll behavior on desktop
- Fixed WordPress core CSS conflicts with scoped selectors
- Removed duplicate field code in timeline editor

**Improvements:**
- Visual design consistency across all admin pages
- Better readability and user experience

### Version 1.0.0
- Initial release: CPT, shortcode, admin settings, per‑timeline overrides, drag-and-drop, SVG/icons, responsive timeline, inertial scrolling

## Donate
If this plugin helped your project, consider supporting development:
- https://www.paypal.me/arnelborresgo

## License
GPL-2.0-or-later

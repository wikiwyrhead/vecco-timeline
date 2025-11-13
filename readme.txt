=== Vecco Timeline ===
Contributors: wikiwyrhead
Donate link: https://www.paypal.me/arnelborresgo
Tags: timeline, slider, horizontal, shortcode, svg, icons
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A flexible horizontal timeline plugin with draggable scroll, custom post type, shortcode rendering, SVG and image icons, drag-and-drop reordering, and per‑timeline styling.

== Description ==

Vecco Timeline lets you build responsive, horizontally scrolling timelines with a WordPress Custom Post Type and a simple shortcode. It supports image or SVG icons (including presets), drag-and-drop item ordering, per‑timeline style overrides (fonts, sizes, colors, separator width), and smooth inertial scrolling.

= Highlights =
- Custom Post Type for managing multiple timelines
- Drag-and-drop reordering in the editor
- Image, SVG paste, or preset icons per item
- Per‑timeline style overrides: base font size, separator color/width, typography
- Global defaults in Settings
- Smooth drag and mouse-wheel scrolling with momentum
- Local assets and no third‑party dependencies by default

== Installation ==
1. Upload the `vecco-timeline` directory to `/wp-content/plugins/` or install via WP Admin.
2. Activate the plugin through the 'Plugins' menu.
3. Go to Timelines > Add New to create a timeline and its items.
4. Insert the shortcode `[vecco_timeline id="123"]` in a page or post, replacing 123 with your timeline post ID.

== Usage ==
- Insert `[vecco_timeline id="{timeline_id}"]` where you want the timeline to render.
- Global defaults are in Timelines > Settings.
- Per‑timeline overrides are available in each timeline edit screen.

== Settings ==
- Accent Color, Icon Size, Base Font Size
- Separator Width (Desktop/Mobile)
- Global Font Family for Year/Title/Description (+ presets)
- Optional global Webfont URL(s)

== Per‑timeline Overrides ==
- Base Font Size, Separator Color
- Year/Title/Description: color, font size, font family (+ presets)
- Per‑timeline Webfont URL(s)

== Shortcode ==
`[vecco_timeline id="123"]`

== Frequently Asked Questions ==
= Why is my webfont not applying? =
Make sure the font-family in settings matches a loaded webfont. Provide a Google Fonts CSS URL (global or per‑timeline) and use the correct family name.

= Can I use inline SVG? =
Yes. Paste sanitized SVG markup. It will inherit the item accent color.

== Screenshots ==
1. Timeline editor with drag-and-drop and icon picker.
2. Frontend horizontal timeline with separators.

== Changelog ==
= 1.3.0 =
* Fixed: Drag-to-reorder functionality in timeline editor now works correctly
* Enhanced: Separator line now aligns with year text center across all viewports (desktop, tablet, mobile)
* Enhanced: Added Settings link to plugin actions on plugins page for quick access
* Improved: Timeline editor JavaScript now properly waits for jQuery UI Sortable to load

= 1.2.0 =
* Enhanced: Redesigned timeline editor with modern 4-column grid layout
* Enhanced: Improved Timeline Settings page with card-based design for better readability
* Enhanced: Added comprehensive instructions explaining global vs per-timeline settings hierarchy
* Enhanced: Improved visual grouping and field organization in admin interface
* Enhanced: Increased font sizes and improved spacing for better accessibility
* Fixed: Mouse wheel horizontal scroll behavior on desktop
* Fixed: WordPress core CSS conflicts with scoped selectors
* Fixed: Removed duplicate field code in timeline editor
* Improved: Visual design consistency across all admin pages
* Improved: Inline help text layout for better user guidance

= 1.0.0 =
* Initial release: CPT, shortcode, admin settings, per‑timeline overrides, drag-and-drop, SVG/icons, responsive timeline, inertial scrolling.

== Upgrade Notice ==
= 1.2.0 =
Major UI improvements: redesigned admin interface with better organization, comprehensive instructions, and enhanced accessibility. Includes bug fixes for mouse wheel scroll and WordPress conflicts.

= 1.0.0 =
Initial release.

# Xarop Theme WordPress Theme

A custom, high-performance, headless-ready WordPress theme built from scratch with **zero plugin dependencies**. This theme is modular, lightweight, fully translatable, and designed for modern WordPress development.

## Features

### Core Capabilities

- **Zero Plugin Dependencies**: No ACF, no Gutenberg, no third-party plugins required
- **Headless-Ready**: Full REST API integration for decoupled architectures
- **Modular Architecture**: Clean, organized file structure for easy maintenance
- **Lightweight**: Minimal footprint with maximum performance
- **Fully Translatable**: Text Domain: `xarop-theme`

### Technical Features

- **Native Meta Boxes**: Custom gallery using WordPress Media Library (wp.media)
- **Custom Post Types**: posts CPT with full REST API support
- **Shared Taxonomy**: Categories work across both Pages and posts
- **REST API Extensions**: Custom endpoints for gallery data and filtered content
- **CSS-Only Menu**: Right-side sliding hamburger menu (100% CSS, 0% JS)
- **Vanilla JavaScript**: No jQuery dependencies, minimal JS footprint
- **Clean Editor**: Content-only editing experience (no design tools in editor)

### Frontend Components

- **Home Slider**: Configurable slider supporting Pages or posts
- **Dynamic Grid**: Category-based filtering with AJAX/REST API
- **Custom Gallery**: Native WordPress media selection with sortable interface
- **Typography-Focused**: Clean, readable design with modern aesthetics

## File Structure

```
xarop-theme/
├── assets/
│   └── js/
│       └── main.js              # Vanilla JavaScript (slider, filters)
├── inc/
│   ├── cleanup.php              # Remove Gutenberg, emojis, embeds
│   ├── post-types.php           # Register posts CPT & Categories taxonomy
│   ├── meta-boxes.php           # Native gallery meta box
│   ├── rest-api.php             # REST API customizations
│   └── ajax-grid.php            # AJAX handlers for filtering
├── archive-post.php          # posts archive template
├── footer.php                   # Footer template
├── front-page.php               # Home page with slider & grid
├── functions.php                # Main theme setup
├── header.php                   # Header with CSS-only menu
├── index.php                    # Main blog template
├── page.php                     # Single page template
├── single-post.php           # Single post template
├── style.css                    # Main stylesheet
└── README.md                    # This file
```

## Installation

1. **Upload the theme**:
   - Copy the `xarop-theme` folder to `wp-content/themes/`
   - Or upload as a ZIP file via WordPress admin

2. **Activate the theme**:
   - Go to Appearance → Themes
   - Activate "Xarop Theme"

3. **Configure menus**:
   - Go to Appearance → Menus
   - Create and assign menus to "Main Menu" and "Footer Menu" locations

4. **Flush permalinks**:
   - Go to Settings → Permalinks
   - Click "Save Changes" (this registers the custom post type URLs)

## Usage

### Creating posts

1. Go to **posts** in the WordPress admin
2. Click **Add New**
3. Enter title and content (native editor, content only)
4. Set a featured image
5. Assign categories (shared with Pages)
6. Add gallery images using the **Custom Gallery** meta box
7. Publish

### Custom Gallery Meta Box

The gallery meta box allows you to:

- Select multiple images from the WordPress Media Library
- Drag and drop to reorder images
- Remove individual images
- Clear all images at once

Gallery IDs are stored as a comma-separated string in `_custom_gallery_ids` meta field.

### Home Slider Configuration

Edit `front-page.php` to configure the slider:

```php
$slider_config = array(
    'type' => 'pages',        // 'pages' or 'posts'
    'ids'  => array(2, 5, 8), // Array of post/page IDs
);
```

### REST API Endpoints

#### Standard WordPress REST API (Extended)

**Pages**:

```
GET /wp-json/wp/v2/pages
GET /wp-json/wp/v2/pages/{id}
```

**posts**:

```
GET /wp-json/wp/v2/posts
GET /wp-json/wp/v2/posts/{id}
```

Both endpoints include:

- `custom_gallery`: Object with gallery IDs and image data
- `shared_categories`: Array of category terms

#### Custom REST API Endpoints

**Filtered posts**:

```
GET /wp-json/xarop-theme/v1/filtered-posts?category={id}&per_page={number}
```

Parameters:

- `category`: Category term ID or 'all' (optional)
- `per_page`: Number of posts to return (default: 12)

### AJAX Handlers

**Filter posts**:

```javascript
fetch(ajaxurl, {
  method: "POST",
  body: new FormData({
    action: "filter_posts",
    category: categoryId,
    per_page: 12,
    nonce: simpleHeadless.nonce,
  }),
});
```

**Get Categories**:

```javascript
fetch(ajaxurl, {
  method: "POST",
  body: new FormData({
    action: "get_categories",
    nonce: simpleHeadless.nonce,
  }),
});
```

## Customization

### Styling

All styles are in `style.css`. The theme uses:

- CSS Grid for layouts
- CSS Custom Properties (variables) can be added
- Mobile-first responsive design
- CSS-only hamburger menu (checkbox hack)

### JavaScript

The `assets/js/main.js` file handles:

- Slider auto-advance (5-second intervals)
- Category filtering via Fetch API
- Menu overlay interactions
- Keyboard navigation for slider

### Adding Custom Fields

To add more meta boxes, edit `inc/meta-boxes.php` and follow the pattern:

```php
function your_custom_meta_box() {
    add_meta_box(
        'your_meta_box_id',
        __('Your Meta Box Title', 'xarop-theme'),
        'your_callback_function',
        array('page', 'post'),
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'your_custom_meta_box');
```

## Performance Optimizations

The theme includes several performance enhancements:

- **Removed Gutenberg**: Block editor and styles completely removed
- **No Emojis**: Emoji scripts and styles disabled
- **No Embeds**: oEmbed functionality removed
- **No jQuery Migrate**: Removed for faster page loads
- **No Version Strings**: Query strings removed from static assets
- **Minimal HTTP Requests**: Only essential assets loaded

## Headless Usage

This theme is headless-ready. To use as a headless CMS:

1. **Enable REST API**: Already enabled by default
2. **Use Custom Endpoints**: Leverage the extended REST API
3. **CORS Configuration**: Add CORS headers if needed:

```php
// In functions.php
add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        return $value;
    });
});
```

4. **Authentication**: Use JWT or Application Passwords for authenticated requests

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Translation

The theme is fully translatable. To create translations:

1. Use a tool like Poedit
2. Scan the theme for strings
3. Create `.po` and `.mo` files
4. Place in `languages/` directory (create if needed)
5. Name files: `xarop-theme-{locale}.po` (e.g., `xarop-theme-es_ES.po`)

## Support

For issues, questions, or contributions:

- Review the code comments in each file
- Check the WordPress Codex for standard functions
- Refer to the REST API Handbook for endpoint documentation

## License

This theme is licensed under the GNU General Public License v2 or later.

## Credits

- Built with native WordPress functions
- No third-party libraries or frameworks
- Designed for performance and simplicity

## Changelog

### Version 1.0.0

- Initial release
- posts custom post type
- Shared categories taxonomy
- Native gallery meta box
- REST API extensions
- CSS-only hamburger menu
- Vanilla JavaScript slider and filters
- Full headless support

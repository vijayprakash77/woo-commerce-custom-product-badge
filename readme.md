# WooCommerce Custom Product Badge
**Contributors:** Vijay Prakash Mahato  
**Tags:** wordpress, woocommerce, product, badge, label, new-arrival, best-seller, hot-deal  
**Requires at least:** 5.6  
**Tested up to:** 6.4  
**Requires PHP:** 5.6.20  
**Stable tag:** 0.0.1  

A lightweight WooCommerce Wordpress plugin that adds custom product badges to selected products with advanced filtering and shortcode support.

## Description

WooCommerce Custom Product Badge is a simple yet powerful plugin that allows you to add eye-catching badges to your WooCommerce products. Perfect for highlighting special products, promotions, or new arrivals.

**Key Features:**
* Easy-to-use product badge system
* Pre-defined badge types (Best Seller, Hot Deal, New Arrival)
* Admin product filtering by badge type
* Shortcode support for displaying badge-specific products
* Responsive design with customizable styling
* Clean integration with WooCommerce product pages

**Available Badge Types:**
* **Best Seller** - Highlight your top-performing products
* **Hot Deal** - Showcase special offers and discounts
* **New Arrival** - Mark recently added products

**How it works:**
1. Add badges to products from the product edit page
2. Badges automatically display on product details pages
3. Filter products by badge type in admin
4. Use shortcodes to display products with specific badges
5. Fully customizable appearance with CSS

## Installation 

**Requirements:**
* WordPress 5.6 or higher
* WooCommerce plugin must be installed and active
* PHP 5.6.20 or higher

**Installation Steps:**
1. Download the plugin ZIP file
2. Log in to your WordPress admin dashboard
3. Navigate to Plugins > Add New > Upload Plugin
4. Click "Choose File" and select the ZIP file
5. Click "Install Now" and then "Activate Plugin"

**Alternative Installation:**
1. Extract the plugin files
2. Upload the plugin folder to `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

## Usage Instructions

**Adding Badges to Products:**
1. Go to Products > All Products in your WordPress admin
2. Edit any product
3. Look for the "Product Badge" meta box in the sidebar
4. Select a badge type from the dropdown
5. Save/Update the product

**Filtering Products by Badge:**
1. Go to Products > All Products
2. Use the "Badge Filter" dropdown at the top
3. Select the badge type you want to filter by
4. Click "Filter" to see only products with that badge

**Using Shortcodes:**
Display products with specific badges anywhere on your site using the `[custom_badge_products]` shortcode.

**Basic Shortcode:**
```
[custom_badge_products badge="best-seller"]
```

**Advanced Shortcode with Parameters:**
```
[custom_badge_products badge="hot-deal" limit="8" columns="4" orderby="date" order="DESC"]
```

## Shortcode Parameters

| Parameter | Description | Default | Options |
|-----------|-------------|---------|---------|
| `badge` | Badge type to display | Required | `best-seller`, `hot-deal`, `new-arrival` |
| `limit` | Number of products to show | All | Any number |
| `columns` | Products per row | 4 | Any number |
| `orderby` | Sort products by | `menu_order title` | `date`, `title`, `menu_order`, `price` |
| `order` | Sort direction | `ASC` | `ASC`, `DESC` |

**Shortcode Examples:**
```
[custom_badge_products badge="new-arrival" limit="6" columns="3"]
[custom_badge_products badge="best-seller" limit="4" columns="2" orderby="date" order="DESC"]
[custom_badge_products badge="hot-deal" columns="1"]
```

## Assumptions Made

**Badge System:**
* Only one badge per product is supported
* Badges are stored as post meta data
* Badge options are predefined and cannot be customized without code changes

**Display Logic:**
* Badges appear on single product pages before the title and price
* Shortcode displays products in a responsive table layout
* CSS files are automatically enqueued on frontend

**Compatibility:**
* Designed for standard WooCommerce installations
* Compatible with most themes that follow WooCommerce standards
* Uses WooCommerce hooks and WordPress best practices

## Frequently Asked Questions

**Q: Can I add custom badge types?**
A: Currently, badge types are predefined. You can modify the `woo_custom_badge_get_options()` function to add custom badges.

**Q: Will badges show on shop/category pages?**
A: By default, badges only show on single product pages. You can add them to shop pages by modifying the plugin hooks.

**Q: Can I change the badge appearance?**
A: Yes, you can customize the badge styling by editing the CSS files in the `assets/css/` directory.

**Q: Does this plugin affect site performance?**
A: The plugin is lightweight and uses efficient WordPress/WooCommerce practices with minimal impact on performance.

**Q: Can I use multiple badges on one product?**
A: Currently, only one badge per product is supported. This is by design to maintain clean product presentation.

**Q: What happens when I deactivate the plugin?**
A: All badge data is automatically removed from the database when the plugin is deactivated.

## Technical Details

**File Structure:**
```
woo-custom-product-badge/
├── woo-custom-product-badge.php (Main plugin file)
└── assets/
    └── css/
        ├── frontend.css (Frontend styles)
        └── admin.css (Admin styles)
```

**Key Functions:**
* `woo_custom_badge_get_options()` - Returns available badge types
* `woo_custom_badge_display()` - Displays badges on product pages
* `woo_custom_badge_shortcode()` - Handles shortcode functionality
* `woo_custom_badge_add_filter()` - Adds admin filtering capability

**Hooks Used:**
* `add_meta_boxes` - Adds badge selection to product edit page
* `save_post` - Saves badge selection
* `woocommerce_single_product_summary` - Displays badges on frontend
* `restrict_manage_posts` - Adds admin filtering
* `parse_query` - Processes badge filtering

**Security Features:**
* Prevents direct file access
* Sanitizes all inputs and outputs
* Uses WordPress nonces for form security
* Validates user permissions before saving data

## Customization

**Adding New Badge Types:**
Modify the `woo_custom_badge_get_options()` function:
```php
return array(
    '' => __('None', 'woo-custom-badge'),
    'best-seller' => __('Best Seller', 'woo-custom-badge'),
    'hot-deal' => __('Hot Deal', 'woo-custom-badge'),
    'new-arrival' => __('New Arrival', 'woo-custom-badge'),
    'limited-edition' => __('Limited Edition', 'woo-custom-badge'), // New badge
);
```

**Customizing Badge Styles:**
Edit `assets/css/frontend.css` to change badge appearance:
```css
.woo-custom-badge {
    /* Your custom styles */
}
```

**Changing Badge Position:**
Modify the hook priority in the `woo_custom_badge_init_hooks()` function:
```php
add_action('woocommerce_single_product_summary', 'woo_custom_badge_display', 15);
```

## Troubleshooting

**Badge not showing on product page:**
1. Ensure WooCommerce is active and working
2. Check if a badge is selected for the product
3. Verify your theme uses standard WooCommerce hooks
4. Clear any caching plugins

**Admin filter not working:**
1. Make sure you're on the Products admin page
2. Check if products actually have badges assigned
3. Try refreshing the page

**Shortcode not displaying products:**
1. Verify the badge parameter matches exactly (`best-seller`, `hot-deal`, `new-arrival`)
2. Ensure products with that badge exist and are published
3. Check for any PHP errors in debug log

**Styling issues:**
1. Check if CSS files are loading properly
2. Look for theme CSS conflicts
3. Try adding `!important` to custom CSS rules

## Changelog

= 0.0.1 =
* Initial release
* Three predefined badge types (Best Seller, Hot Deal, New Arrival)
* Admin product filtering by badge type
* Shortcode support with customizable parameters
* Responsive product display
* WooCommerce integration

## Upgrade Notice

= 0.0.1 =
Initial release of WooCommerce Custom Product Badge plugin.

## Development Notes

**Code Standards:**
* Follows WordPress coding standards
* All functions prefixed with `woo_custom_badge_` to avoid conflicts
* Proper sanitization and escaping throughout
* Comprehensive error checking and validation

**Performance Considerations:**
* Minimal database queries
* Efficient meta data handling
* CSS files only loaded when needed
* Clean uninstall process

**Extensibility:**
* Hook-based architecture allows easy customization
* Modular function design
* Clear separation of admin and frontend functionality

## Support

For support, feature requests, or bug reports, please contact:
**Email:** vijayprakashmh@gmail.com

**Plugin Information:**
* Version: 0.0.1
* Tested with: WooCommerce 9.9.5, WordPress 6.4
* License: MIT License
* Text Domain: woo-custom-badge

---

**Note:** Make sure to create the `assets/css/` directory structure and add your `frontend.css` and `admin.css` files for proper styling.
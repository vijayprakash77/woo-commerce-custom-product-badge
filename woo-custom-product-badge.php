<?php
/*
Plugin Name: Woo Custom Product Badge
Plugin URI: https://test.com/
Description: A lightweight WooCommerce plugin that adds custom product badges to selected products with advanced filtering and shortcode support.
Version: 0.0.1
Requires PHP: 5.6.20
Author: Vijay Prakash Mahato
Author URI: https://test.com
Text Domain: Woo Custom Product Badge
*/

if (!defined('ABSPATH')) {
    exit;
}

define('WOO_CUSTOM_BADGE_VERSION', '1.0.0');
define('WOO_CUSTOM_BADGE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WOO_CUSTOM_BADGE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WOO_CUSTOM_BADGE_META_KEY', '_woo_custom_badge');

function woo_custom_badge_get_options() {

    return array(
        '' => __('None', 'woo-custom-badge'),
        'best-seller' => __('Best Seller', 'woo-custom-badge'),
        'hot-deal' => __('Hot Deal', 'woo-custom-badge'),
        'new-arrival' => __('New Arrival', 'woo-custom-badge')
    );
}

function woo_custom_badge_init() {

    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'woo_custom_badge_woocommerce_missing_notice');
        return;
    }
    woo_custom_badge_init_hooks();
}

add_action('plugins_loaded', 'woo_custom_badge_init');

function woo_custom_badge_init_hooks() {

    // Admin hooks
    add_action('add_meta_boxes', 'woo_custom_badge_add_meta_box');
    add_action('save_post', 'woo_custom_badge_save_meta_box');
    add_action('restrict_manage_posts', 'woo_custom_badge_add_filter');
    add_action('parse_query', 'woo_custom_badge_filter_products');

    // Frontend hooks
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    add_action('woocommerce_single_product_summary', 'woo_custom_badge_display', 4);
    add_action('wp_enqueue_scripts', 'woo_custom_badge_enqueue_frontend_styles');
    add_action('admin_enqueue_scripts', 'woo_custom_badge_enqueue_admin_styles');

    // Shortcode
    add_shortcode('custom_badge_products', 'woo_custom_badge_shortcode');
}

function woo_custom_badge_woocommerce_missing_notice() {

    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e('Woo Custom Product Badge requires WooCommerce to be installed and active.', 'woo-custom-badge'); ?></p>
    </div>
    <?php
}

function woo_custom_badge_add_meta_box() {

    add_meta_box(
        'woo_custom_badge_meta_box',
        __('Product Badge', 'woo-custom-badge'),
        'woo_custom_badge_meta_box_callback',
        'product',
        'side',
        'default'
    );
}


function woo_custom_badge_meta_box_callback($post) {

    wp_nonce_field('woo_custom_badge_save_meta_box', 'woo_custom_badge_meta_box_nonce');
    
    $current_badge = get_post_meta($post->ID, WOO_CUSTOM_BADGE_META_KEY, true);
    $badge_options = woo_custom_badge_get_options();
    
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="woo_custom_badge"><?php esc_html_e('Badge Type', 'woo-custom-badge'); ?></label>
            </th>
            <td>
                <select name="woo_custom_badge" id="woo_custom_badge">
                    <?php foreach ($badge_options as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php echo ($current_badge === $value) ? 'selected="selected"' : ''; ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td> 
        </tr>
		  <tr>
                <p class="description"><?php esc_html_e('Select a badge to display on the product page.', 'woo-custom-badge'); ?></p>
          </tr>
    </table>
    <?php
}

function woo_custom_badge_save_meta_box($post_id) {

    if (!isset($_POST['woo_custom_badge_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['woo_custom_badge_meta_box_nonce'], 'woo_custom_badge_save_meta_box')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (get_post_type($post_id) !== 'product') {
        return;
    }

    if (isset($_POST['woo_custom_badge'])) {

        $badge_value = sanitize_text_field($_POST['woo_custom_badge']);
        $badge_options = woo_custom_badge_get_options();
        
        if (array_key_exists($badge_value, $badge_options)) {
            if (empty($badge_value)) {
                delete_post_meta($post_id, WOO_CUSTOM_BADGE_META_KEY);
            } else {
                update_post_meta($post_id, WOO_CUSTOM_BADGE_META_KEY, $badge_value);
            }
        }
    }
}

function woo_custom_badge_add_filter() {

    global $typenow;
    
    if ($typenow === 'product') {
        $selected = isset($_GET['badge_filter']) ? sanitize_text_field($_GET['badge_filter']) : '';
        $badge_options = woo_custom_badge_get_options();
        ?>
        <select name="badge_filter" id="badge_filter">
            <option value=""><?php esc_html_e('All Badges', 'woo-custom-badge'); ?></option>
            <?php foreach ($badge_options as $value => $label) : ?>
                <?php if (!empty($value)) : ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php echo ($selected === $value) ? 'selected="selected"' : ''; ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <?php
    }
}


function woo_custom_badge_filter_products($query) {

    global $pagenow;
    
    if ($pagenow === 'edit.php' && 
        isset($_GET['post_type']) && $_GET['post_type'] === 'product' &&
        isset($_GET['badge_filter']) && !empty($_GET['badge_filter'])) {
        
        $badge_filter = sanitize_text_field($_GET['badge_filter']);
        $badge_options = woo_custom_badge_get_options();
        
        if (array_key_exists($badge_filter, $badge_options)) {
            $query->query_vars['meta_key'] = WOO_CUSTOM_BADGE_META_KEY;
            $query->query_vars['meta_value'] = $badge_filter;
        }
    }
}

function woo_custom_badge_display() {

    global $product;
    
    if (!$product) {
        return;
    }
    
    $badge = get_post_meta($product->get_id(), WOO_CUSTOM_BADGE_META_KEY, true);
    $badge_options = woo_custom_badge_get_options();
    
    if (!empty($badge) && array_key_exists($badge, $badge_options)) {
        $badge_label = $badge_options[$badge];
        ?>
        <div class="woo-custom-badge">
            <span class="badge-text"><?php echo esc_html($badge_label); ?></span>
        </div>
        <?php
    }
}

function woo_custom_badge_shortcode($atts) {

    $atts = shortcode_atts(array(
        'badge' => '',
        'limit' => '',
        'columns' => '',
        'orderby' => 'menu_order title',
        'order' => 'ASC'
    ), $atts, 'custom_badge_products');

    $badge_options = woo_custom_badge_get_options();

    if (empty($atts['badge']) || !array_key_exists($atts['badge'], $badge_options)) {
        return '<p>' . esc_html__('Invalid badge parameter.', 'woo-custom-badge') . '</p>';
    }

    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => sanitize_text_field($atts['orderby']),
        'order' => sanitize_text_field($atts['order']),
        'meta_query' => array(
            array(
                'key' => WOO_CUSTOM_BADGE_META_KEY,
                'value' => sanitize_text_field($atts['badge']),
                'compare' => '='
            )
        )
    );

    $products = new WP_Query($args);

    if (!$products->have_posts()) {
        return '<p>' . esc_html__('No products found with this badge.', 'woo-custom-badge') . '</p>';
    }
    
	$columns = intval($atts['columns']);

    ob_start();
    ?>
		 <div class="woo-custom-badge-products">
			<table class="custom-badge-product-table" style="width:100%; border-collapse: collapse;">
				<?php
				$count = 0;
				echo '<tr>';
				while ($products->have_posts()) {
					$products->the_post();
					global $product;

					$badge = get_post_meta($product->get_id(), WOO_CUSTOM_BADGE_META_KEY, true);
					$badge_label = !empty($badge) && array_key_exists($badge, $badge_options) ? $badge_options[$badge] : '';

					echo '<td style="vertical-align:top; width:' . (100 / $columns) . '%; border: 1px solid #ddd; padding: 10px;">';
					?>
					<div class="product-item">
						<div class="product-image">
							<a href="<?php echo esc_url(get_permalink()); ?>">
								<?php echo woocommerce_get_product_thumbnail(); ?>
							</a>
						</div>

						<div class="product-details">
							<?php if (!empty($badge_label)) : ?>
								<div class="shortcode-badge">
									<span class="badge-text"><?php echo esc_html($badge_label); ?></span>
								</div>
							<?php endif; ?>

							<h3 class="product-title">
								<a href="<?php echo esc_url(get_permalink()); ?>">
									<?php echo esc_html(get_the_title()); ?>
								</a>
							</h3>

							<div class="product-price">
								<?php echo $product->get_price_html(); ?>
							</div>

							<div class="product-actions">
								<?php woocommerce_template_loop_add_to_cart(); ?>
							</div>
						</div>
					</div>
					<?php
					echo '</td>'; 

					$count++;
					if ($count % $columns === 0) {//when no of product need to show inside in one row
						echo '</tr>';
						if ($products->current_post + 1 < $products->post_count) {
							echo '<tr>';
						}
					}
				}

				// Fill empty columns if last row is not complete
				$remaining = $count % $columns;
				if ($remaining !== 0) {
					for ($i = 0; $i < ($columns - $remaining); $i++) {
						echo '<td style="width:' . (100 / $columns) . '%;"></td>';
					}
					echo '</tr>';
				}
				wp_reset_postdata();
				?>
			</table>
		</div>

    <?php
    return ob_get_clean();
}

function woo_custom_badge_enqueue_frontend_styles() {
	
        wp_enqueue_style(
            'woo-custom-badge-frontend',
            WOO_CUSTOM_BADGE_PLUGIN_URL . 'assests/css/frontend.css',
            array(),
            WOO_CUSTOM_BADGE_VERSION
        );
}

function woo_custom_badge_enqueue_admin_styles($hook) {

    if ($hook === 'post.php') {
        global $post_type;
        if ($post_type === 'product') {
            wp_enqueue_style(
                'woo-custom-badge-admin',
                WOO_CUSTOM_BADGE_PLUGIN_URL . 'assests/css/admin.css',
                array(),
                WOO_CUSTOM_BADGE_VERSION
            );
        }
    }
}

function woo_custom_badge_deactivate() {

    delete_post_meta_by_key(WOO_CUSTOM_BADGE_META_KEY);
  
}

register_deactivation_hook(__FILE__, 'woo_custom_badge_deactivate');

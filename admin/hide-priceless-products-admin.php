<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.giovannimalagnino.eu
 * @since      1.0.0
 *
 * @package    Hide_Priceless_Products
 * @subpackage Hide_Priceless_Products/admin
 * @author     Giovanni Malagnino <contact@giovannimalagnino.eu>
 */

/**
 * Check if WooCommerce is active
 **/

if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Enqueue scripts and styles
 */

function load_custom_wp_admin_style($hook) {
	
	if($hook != 'woocommerce_page_wc-settings') {
		return;
	}

	wp_register_style('hpp_admin_stylesheet', '/wp-content/plugins/'.dirname(dirname(plugin_basename(__FILE__))) . '/CSS/style.css');

	wp_enqueue_style('hpp_admin_stylesheet');
	
	wp_register_script('hpp_admin_scripts','/wp-content/plugins/'.dirname(dirname(plugin_basename(__FILE__))) . '/js/scripts.js', array(), false, true);

	wp_enqueue_script('hpp_admin_scripts');

}

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );

/* Loads languages */

add_action('plugins_loaded', 'hpp_plugin_init'); 

function hpp_plugin_init() {

		load_plugin_textdomain( 'hide-priceless-products', false, dirname(dirname(plugin_basename(__FILE__))).'/languages/' );

}

function hpp_sample_admin_notice__success() {
    ?>
    <div class="notice notice-error">
        <p><?php _e( 'WooCommerce must be installed and activated if you want to use Hide Priceless Products', 'hide-priceless-products' ); ?></p>
    </div><?php
}

if (!is_woocommerce_activated()){
add_action( 'admin_notices', 'hpp_sample_admin_notice__success' );
}

require_once( dirname(dirname( __FILE__ )) . '/lib/functions.php' );

/* Add option in the WooCommerce products general settings tab*/

function hidepricelessproducts_all_settings( $settings, $current_section) {
	
	$categories_array = hpp_get_cat();
	$categories_name = $categories_array[1];
	$categories_slug = $categories_array[3];

	if ( $current_section == '' ) {
        
        $settings_update = $settings;
		
		// Add Title to the Settings
		
		$settings_update[] = array(
			'name' => __( 'Hide priceless products', 'hide-priceless-products' ),
			'type' => 'title',
			'desc' => __( 'The following options are used to configure Hide Priceless products', 'hide-priceless-products' ),
			'id' => 'hidepricelessproductstitle' );
		
		// Add first checkbox option
		
		$settings_update[] = array(
			'name'     => __( 'Hide products without price', 'hide-priceless-products' ),
			'desc_tip' => __( 'This will automatically hide the product without assigned price', 'hide-priceless-products' ),
			'id'       => 'hidepricelessproductscheckbox',
			'type'     => 'checkbox',
			'desc'     => __( 'Hide priceless products', 'hide-priceless-products' ),
		);
			
		// Add first categories options
		
		$settings_update[] = array(
			'name'     => __( 'Categories to keep', 'hide-priceless-products' ),
			'desc_tip' => __( 'Select the product categories you want to keep visible', 'hide-priceless-products' ),
			'id'       => 'pricelesscategoriesmultiselect',
			'type'     => 'multiselect',
			'options' => $categories_name,	
		);
		
		// Add second option for price equal to 0
		
		$settings_update[] = array(
			'name'     => __( 'Hide products which price is set to 0', 'hide-priceless-products' ),
			'desc_tip' => __( 'This will automatically hide the product which price is set to 0', 'hide-priceless-products' ),
			'id'       => 'hidezeropriceproductscheckbox',
			'type'     => 'checkbox',
			'desc'     => __( 'Hide product with a price of 0', 'hide-priceless-products' ),
		);
		
		$settings_update[] = array(
			'name'     => __( 'Categories to keep', 'hide-priceless-products' ),
			'desc_tip' => __( 'Select the product categories you want to keep visible', 'hide-priceless-products' ),
			'id'       => 'zerocategoriesmultiselect',
			'type'     => 'multiselect',
			'options' => $categories_name,
		);
		
		$settings_update[] = array(
			'type' => 'sectionend',
			'id' => 'hidepricelessproducts'
		);
		return $settings_update;
	
	/**
	 * If not, return the standard settings
	 **/
	
	} else {
		return $settings;
	}
}

add_filter( 'woocommerce_get_settings_products', 'hidepricelessproducts_all_settings', 10, 2 );
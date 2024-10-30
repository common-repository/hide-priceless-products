<?php
/*
 * Plugin Name: Hide Priceless products
 * Version: 1.1.1
 * Plugin URI: http://www.giovannimalagnino.eu/hide-priceless-products
 * Description: Hide products without price or hide product which price is 0. Selected categories can remain visible. Simply. Go to WooCommerce -> Settings -> Products
 * Author: Giovanni Malagnino Consulting
 * Author URI: http://www.giovannimalagnino.eu/
 * Requires at least: 4.0
 * Tested up to: 5.2
 *
 * WC tested up to: 3.6.3
 *
 * Text Domain: hide-priceless-products
 * Domain Path: /languages/
 *
 * @package WordPress
 * @author Giovanni Malagnino Consulting
 * @since 1.0
 */

if (! defined('ABSPATH')) {
    exit;
}



if (is_admin()) {
    // we are in admin mode
    require_once(dirname(__FILE__) . '/admin/hide-priceless-products-admin.php');
}

require_once(dirname(__FILE__) . '/lib/functions.php');



/* Main functions */

function hpp_the_main()
{
    global $wp_query, $wpdb;
    
    if (!is_admin() && function_exists('is_woocommerce')) {
        if (is_woocommerce()) {
            function posts_join_statement($join)
            {
                global $wp_query, $wpdb;
                if (!is_tax()) {
                    $join .= "
                LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
                LEFT JOIN $wpdb->term_relationships ON $wpdb->posts.ID = $wpdb->term_relationships.object_id";
                } else {
                    $join .= "
                LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id";
                }
                return $join;
            }
        
            add_filter('posts_join', 'posts_join_statement');
        
            function posts_where_statement($where)
            {
                //gets the global query var object
               
                global $wp_query, $wpdb;
                
                $pricelesscatsids = hpp_priceless_cat_ids();
                $pricelesscatsnames = hpp_priceless_cat_names();
                $zerocatsids = hpp_priceless_cat_ids();

                $hide_categories_priceless = get_option('pricelesscategoriesmultiselect');
                $hide_priceless_products = get_option('hidepricelessproductscheckbox');
                $hide_zero_products = get_option('hidezeropriceproductscheckbox');
                $hide_categories_zero = get_option('zerocategoriesmultiselect');
                $selectedpricelesscats = array();
                $selectedzerocats = array();
            
                foreach ($hide_categories_priceless as $key) {
                    $selectedpricelesscats[] = $pricelesscatsids[$key];
                }
            
                foreach ($hide_categories_zero as $key) {
                    $selectedzerocats[] = $zerocatsids[$key];
                }
        
            
                if (is_tax('product_cat')) {
                    $curr_prod_cat[0] = get_queried_object()->term_taxonomy_id;
                    $selectedzerocats = array_intersect($selectedzerocats, $curr_prod_cat);
                    $selectedpricelesscats = array_intersect($selectedpricelesscats, $curr_prod_cat);
                }
                
                
                //if (!empty($hide_categories_priceless) && !empty($hide_categories_zero)){
                    
                if ($hide_zero_products == 'no' && $hide_priceless_products == 'yes') {
                    if (!empty($selectedpricelesscats)) {
                        $where .= " AND (
                            $wpdb->posts.post_status = 'publish'
                        AND
                            $wpdb->posts.post_type = 'product'
                        AND
                            ( 
                                $wpdb->posts.ID IN (
                                    SELECT $wpdb->term_relationships.object_id
                                    FROM $wpdb->term_relationships
                                    WHERE $wpdb->term_relationships.term_taxonomy_id IN ('".implode("','", $selectedpricelesscats)."') 
                                )
                            )
                        AND
                            ($wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value = '')
                        
                        OR
                            ($wpdb->posts.post_status = 'publish' AND
                            $wpdb->posts.post_type = 'product' AND
                            $wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value != ''
                            )
                    )";
                    } else {
                        $where .= " AND (
                            
                            ($wpdb->posts.post_status = 'publish' AND
                            $wpdb->posts.post_type = 'product' AND
                            $wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value != ''
                            )
                    )";
                    }
                } elseif ($hide_zero_products == 'yes' && $hide_priceless_products == 'yes') {
                    if (!empty($selectedpricelesscats) && !empty($selectedzerocats)) {
                        $where .= " AND (
                                $wpdb->posts.post_status = 'publish'
                            AND
                                $wpdb->posts.post_type = 'product'
                            AND
                                ( 
                                    $wpdb->posts.ID IN (
                                        SELECT $wpdb->term_relationships.object_id
                                        FROM $wpdb->term_relationships
                                        WHERE $wpdb->term_relationships.term_taxonomy_id IN ('".implode("','", $selectedzerocats)."') 
                                    )
                                )
                            AND
                                ($wpdb->postmeta.meta_key = '_price' AND
                                $wpdb->postmeta.meta_value = '0')
                            OR
                                (   $wpdb->posts.post_status = 'publish'
                                AND
                                    $wpdb->posts.post_type = 'product'
                                AND
                                ( 
                                    $wpdb->posts.ID IN (
                                        SELECT $wpdb->term_relationships.object_id
                                        FROM $wpdb->term_relationships
                                        WHERE $wpdb->term_relationships.term_taxonomy_id IN ('".implode("','", $selectedpricelesscats)."') 
                                    )
                                )
                                AND
                                    ($wpdb->postmeta.meta_key = '_price' AND
                                    $wpdb->postmeta.meta_value = '')
                                )
                            OR
                                ($wpdb->posts.post_status = 'publish' AND
                                $wpdb->posts.post_type = 'product' AND
                                $wpdb->postmeta.meta_key = '_price' AND
                                $wpdb->postmeta.meta_value != '' AND
                                $wpdb->postmeta.meta_value != '0'
                                )
                        )";
                    } elseif (!empty($selectedpricelesscats) && empty($selectedzerocats)) {
                        $where .= " AND (
                            $wpdb->posts.post_status = 'publish'
                        AND
                            $wpdb->posts.post_type = 'product'
                        AND
                            ( 
                                $wpdb->posts.ID IN (
                                    SELECT $wpdb->term_relationships.object_id
                                    FROM $wpdb->term_relationships
                                    WHERE $wpdb->term_relationships.term_taxonomy_id IN ('".implode("','", $selectedpricelesscats)."') 
                                )
                            )
                        AND
                            ($wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value = '')
                        
                        OR
                            ($wpdb->posts.post_status = 'publish' AND
                            $wpdb->posts.post_type = 'product' AND
                            $wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value != '' AND
                            $wpdb->postmeta.meta_value != '0'
                            )
                    )";
                    } elseif (empty($selectedpricelesscats) && !empty($selectedzerocats)) {
                        $where .= " AND (
                            $wpdb->posts.post_status = 'publish'
                        AND
                            $wpdb->posts.post_type = 'product'
                        AND
                            ( 
                                $wpdb->posts.ID IN (
                                    SELECT $wpdb->term_relationships.object_id
                                    FROM $wpdb->term_relationships
                                    WHERE $wpdb->term_relationships.term_taxonomy_id IN ('".implode("','", $selectedzerocats)."') 
                                )
                            )
                        AND
                            ($wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value = '0')
                        OR
                            ($wpdb->posts.post_status = 'publish' AND
                            $wpdb->posts.post_type = 'product' AND
                            $wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value != '' AND
                            $wpdb->postmeta.meta_value != '0'
                            )
                    )";
                    } elseif (empty($selectedpricelesscats) && empty($selectedzerocats)) {
                        $where .= " AND (
                            
                            ($wpdb->posts.post_status = 'publish' AND
                            $wpdb->posts.post_type = 'product' AND
                            $wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value != '' AND
                            $wpdb->postmeta.meta_value != '0'
                            )
                    )";
                    }
                } elseif ($hide_zero_products == 'yes' && $hide_priceless_products == 'no') {
                    if (!empty($selectedzerocats)) {
                        $where .= " AND (
                            $wpdb->posts.post_status = 'publish'
                        AND
                            $wpdb->posts.post_type = 'product'
                        AND
                            ( 
                                $wpdb->posts.ID IN (
                                    SELECT $wpdb->term_relationships.object_id
                                    FROM $wpdb->term_relationships
                                    WHERE $wpdb->term_relationships.term_taxonomy_id IN ('".implode("','", $selectedzerocats)."') 
                                )
                            )
                        AND
                            ($wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value = '0')
                        
                        OR
                            ($wpdb->posts.post_status = 'publish' AND
                            $wpdb->posts.post_type = 'product' AND
                            $wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value != '0'
                            )
                    )";
                    } else {
                        $where .= " AND (
                            
                            ($wpdb->posts.post_status = 'publish' AND
                            $wpdb->posts.post_type = 'product' AND
                            $wpdb->postmeta.meta_key = '_price' AND
                            $wpdb->postmeta.meta_value != '0'
                            )
                    )";
                    }
                } else {
                    $where .= " AND (
                        $wpdb->posts.post_status = 'publish'
                    AND
                        $wpdb->posts.post_type = 'product'
                    
                )";
                }
                
               
            
                //removes the actions hooked on the '__after_loop' (post navigation)
            
            
                remove_all_actions('__after_loop');
                
                //highlight_string("WHEREBEGIN data =\n" . var_export($where, true) . ";WHEREEND\n");
         
                return $where;
            }
            
            
            add_filter('posts_where', 'posts_where_statement');
        }
    }
}
add_action('woocommerce_product_query', 'hpp_the_main');

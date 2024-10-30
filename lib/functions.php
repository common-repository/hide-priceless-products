<?php
/**
 * The global functionalities of the plugin.
 *
 * @link       https://www.giovannimalagnino.eu
 * @since      1.1.0
 *
 * @package    Hide_Priceless_Products
 * @subpackage Hide_Priceless_Products/admin
 * @author     Giovanni Malagnino <contact@giovannimalagnino.eu>
 */

/* Get product categories */

function hpp_get_cat(){ 
	$taxonomy     = 'product_cat';
	$orderby      = 'name';  
	$show_count   = true;      // 1 for yes, 0 for no
	$pad_counts   = false;      // 1 for yes, 0 for no
	$hierarchical = false;      // 1 for yes, 0 for no  
	$title        = 'categoriez';  
	$empty        = false;

	$args = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'show_count'   => $show_count,
			'pad_counts'   => $pad_counts,
			'hierarchical' => $hierarchical,
			'title_li'     => $title,
			'hide_empty'   => $empty
	);
	$prd_cat = get_terms( $args );

	$i = 0;
	foreach ($prd_cat as $bla){
		$catname[] = $bla->name;
		$catid[] = $bla->term_taxonomy_id;
		$catslug[] = $bla->slug;
		$i += 1;
	}

return array($prd_cat, $catname, $catid, $catslug);	
} 

function hpp_priceless_cat_slugs(){
    $categories_array = hpp_get_cat();
	$categories_slugs = $categories_array[3];
    return $categories_slugs;
}

function hpp_priceless_cat_ids(){
    $categories_array = hpp_get_cat();
	$categories_id = $categories_array[2];
    return $categories_id;
}

function hpp_priceless_cat_names(){
    $categories_array = hpp_get_cat();
	$categories_names = $categories_array[1];
    return $categories_names;
}
<?php


function hpp_hide_priceless_products( $q ){
    
    $pricelesscatslugs = hpp_priceless_cat_slugs();
    $pricelesscatsids = hpp_priceless_cat_ids();
    $pricelesscatsnames = hpp_priceless_cat_names();
    $zerocatsids = hpp_priceless_cat_ids();
    
    $hide_categories_priceless = get_option('pricelesscategoriesmultiselect');
    $hide_priceless_products = get_option('hidepricelessproductscheckbox');
    $hide_zero_products = get_option('hidezeropriceproductscheckbox');
    $hide_categories_zero = get_option('zerocategoriesmultiselect');
    $selectedpricelesscats = array();
    $selectedzerocats = array();

    //Var_dump($hide_priceless_products);
    //Var_dump($hide_zero_products);

    foreach ($hide_categories_priceless as $key){
        $selectedpricelesscats[] = $pricelesscatsids[$key];
    }
    
    foreach ($hide_categories_zero as $key){
        $selectedzerocats[] = $zerocatsids[$key];
    }

    global $wpdb;

    if ($hide_zero_products == 'no' && $hide_priceless_products == 'yes' ){
        
        /* $selectedpricenotin = $selectedpricelesscats;
        $selectedpricenotin = "('".implode("','",$selectedpricenotin)."')"; */
        //var_dump ($selectedpricenotin);
        //global $wpdb;
        if (!empty($selectedpricelesscats)){
            $sql = $wpdb->prepare("
                SELECT DISTINCT
                    p.ID,
                    p.post_title
                FROM
                    wp_posts p
                LEFT JOIN wp_postmeta m ON
                    m.post_id = p.ID
                LEFT JOIN wp_term_relationships r ON
                    r.object_id = p.ID
                LEFT JOIN wp_term_taxonomy tt ON
                    tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                LEFT JOIN wp_terms t ON
                    t.term_id = tt.term_id
                WHERE
                    /* DONE (p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    tt.term_taxonomy_id IN ('".implode("','",$selectedpricelesscats)."') AND
                    m.meta_key = '_price' AND
                    m.meta_value = '') OR
                    (
                        p.post_status = 'publish' AND
                        p.post_type = 'product' AND
                        m.meta_key = '_price' AND
                        m.meta_value != %s
                    ) */
                    ORDER BY
                    p.post_title"
            ,array(''));
        } else {
            $sql = $wpdb->prepare("
                SELECT DISTINCT
                    p.ID,
                    p.post_title
                FROM
                    wp_posts p
                LEFT JOIN wp_postmeta m ON
                    m.post_id = p.ID
                LEFT JOIN wp_term_relationships r ON
                    r.object_id = p.ID
                LEFT JOIN wp_term_taxonomy tt ON
                    tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                LEFT JOIN wp_terms t ON
                    t.term_id = tt.term_id
                WHERE
                    p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    m.meta_key = '_price' AND
                    m.meta_value != %s
                ORDER BY
                    p.post_title"
            ,array(''));
        }
        //highlight_string("ZERO_NO_PRICELESS_YES data =\n" . var_export($sql, true) . ";\n");
       
    }

    else if ($hide_zero_products == 'yes' && $hide_priceless_products == 'yes' ){
        
        /* $selectedpricenotin = $selectedpricelesscats;
        $selectedpricenotin = "('".implode("','",$selectedpricenotin)."')"; */
        //var_dump ($selectedpricenotin);
        //global $wpdb;
        if (!empty($selectedpricelesscats) && !empty($selectedzerocats)){
            $sql = $wpdb->prepare("
                SELECT DISTINCT
                    p.ID,
                    p.post_title
                FROM
                    wp_posts p
                LEFT JOIN wp_postmeta m ON
                    m.post_id = p.ID
                LEFT JOIN wp_term_relationships r ON
                    r.object_id = p.ID
                LEFT JOIN wp_term_taxonomy tt ON
                    tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                LEFT JOIN wp_terms t ON
                    t.term_id = tt.term_id
                WHERE
                    /* (p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    tt.term_taxonomy_id IN ('".implode("','",$selectedzerocats)."') AND
                    m.meta_key = '_price' AND
                    m.meta_value = %s)
                        OR
                    (p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    tt.term_taxonomy_id IN ('".implode("','",$selectedpricelesscats)."') AND
                    m.meta_key = '_price' AND
                    m.meta_value = %s)
                        OR
                (
                    p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    m.meta_key = '_price' AND
                    m.meta_value != %s AND
                    m.meta_value != %s
                ) */
                ORDER BY
                    p.post_title"
            ,array('0','','','0'));
        }
        
        else if (!empty($selectedpricelesscats) && empty($selectedzerocats)) {
            $sql = $wpdb->prepare("
                SELECT DISTINCT
                    p.ID,
                    p.post_title
                FROM
                    wp_posts p
                LEFT JOIN wp_postmeta m ON
                    m.post_id = p.ID
                LEFT JOIN wp_term_relationships r ON
                    r.object_id = p.ID
                LEFT JOIN wp_term_taxonomy tt ON
                    tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                LEFT JOIN wp_terms t ON
                    t.term_id = tt.term_id
                WHERE
                    (p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    tt.term_taxonomy_id IN ('".implode("','",$selectedpricelesscats)."') AND
                    m.meta_key = '_price' AND
                    m.meta_value = %s)
                        OR
                    (
                        p.post_status = 'publish' AND
                        p.post_type = 'product' AND
                        m.meta_key = '_price' AND
                        m.meta_value != %s AND
                        m.meta_value != %s
                    )
                ORDER BY
                    p.post_title"
            ,array('','0',''));
            var_dump($selectedpricelesscats);
        }
        
        else if (empty($selectedpricelesscats) && !empty($selectedzerocats)) {
            $sql = $wpdb->prepare("
                SELECT DISTINCT
                    p.ID,
                    p.post_title
                FROM
                    wp_posts p
                LEFT JOIN wp_postmeta m ON
                    m.post_id = p.ID
                LEFT JOIN wp_term_relationships r ON
                    r.object_id = p.ID
                LEFT JOIN wp_term_taxonomy tt ON
                    tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                LEFT JOIN wp_terms t ON
                    t.term_id = tt.term_id
                WHERE
                    (p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    tt.term_taxonomy_id IN ('".implode("','",$selectedzerocats)."') AND
                    m.meta_key = '_price' AND
                    m.meta_value = %s)
                        OR
                    (
                        p.post_status = 'publish' AND
                        p.post_type = 'product' AND
                        m.meta_key = '_price' AND
                        m.meta_value != %s AND
                        m.meta_value != %s
                    )
                ORDER BY
                p.post_title"
            ,array('0','0',''));
        }

        else if (empty($selectedpricelesscats) && empty($selectedzerocats)) {
            $sql = $wpdb->prepare("
                SELECT DISTINCT
                    p.ID,
                    p.post_title
                FROM
                    wp_posts p
                LEFT JOIN wp_postmeta m ON
                    m.post_id = p.ID
                LEFT JOIN wp_term_relationships r ON
                    r.object_id = p.ID
                LEFT JOIN wp_term_taxonomy tt ON
                    tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                LEFT JOIN wp_terms t ON
                    t.term_id = tt.term_id
                WHERE
                    (
                        p.post_status = 'publish' AND
                        p.post_type = 'product' AND
                        m.meta_key = '_price' AND
                        m.meta_value != %s AND
                        m.meta_value != %s
                    )
                ORDER BY
                p.post_title"
                
            ,array('0',''));
        }
        
    }
    
    else if ($hide_zero_products == 'yes' && $hide_priceless_products == 'no' ){
        
        /* $selectedpricenotin = $selectedpricelesscats;
        $selectedpricenotin = "('".implode("','",$selectedpricenotin)."')"; */
        //var_dump ($selectedpricenotin);
        //global $wpdb;
        if (!empty($selectedzerocats)){
            $sql = $wpdb->prepare("
                SELECT DISTINCT
                    p.ID,
                    p.post_title
                FROM
                    wp_posts p
                LEFT JOIN wp_postmeta m ON
                    m.post_id = p.ID
                LEFT JOIN wp_term_relationships r ON
                    r.object_id = p.ID
                LEFT JOIN wp_term_taxonomy tt ON
                    tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                LEFT JOIN wp_terms t ON
                    t.term_id = tt.term_id
                WHERE
                    (p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    tt.term_taxonomy_id IN ('".implode("','",$selectedzerocats)."') AND
                    m.meta_key = '_price' AND
                    m.meta_value = %s) OR
                    (
                        p.post_status = 'publish' AND
                        p.post_type = 'product' AND
                        m.meta_key = '_price' AND
                        m.meta_value != %s
                    )
                ORDER BY
                    p.post_title"
            ,array('0','0'));
        } else {
            $sql = $wpdb->prepare("
                SELECT DISTINCT
                    p.ID,
                    p.post_title
                FROM
                    wp_posts p
                LEFT JOIN wp_postmeta m ON
                    m.post_id = p.ID
                LEFT JOIN wp_term_relationships r ON
                    r.object_id = p.ID
                LEFT JOIN wp_term_taxonomy tt ON
                    tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                LEFT JOIN wp_terms t ON
                    t.term_id = tt.term_id
                WHERE
                    p.post_status = 'publish' AND
                    p.post_type = 'product' AND
                    m.meta_key = '_price' AND
                    m.meta_value != %s
                ORDER BY
                    p.post_title"
            ,array('0'));
        }
        //highlight_string("WPQUERYREQUESTBEGIN data =\n" . var_export($sql, true) . ";WPQUERYREQUESTBEGIN\n");
       
    }
    
    else {
        $sql = $wpdb->prepare("
            SELECT DISTINCT
                p.ID,
                p.post_title
            FROM
                wp_posts p
            LEFT JOIN wp_postmeta m ON
                m.post_id = p.ID
            LEFT JOIN wp_term_relationships r ON
                r.object_id = p.ID
            LEFT JOIN wp_term_taxonomy tt ON
                tt.term_taxonomy_id = r.term_taxonomy_id AND tt.taxonomy = 'product_cat'
            LEFT JOIN wp_terms t ON
                t.term_id = tt.term_id
            WHERE
                p.post_status = 'publish' AND
                p.post_type = %s
            ORDER BY
                p.post_title
        "
        ,array('product'));
    }
        
    //highlight_string("WPQUERYREQUESTBEGIN data =\n" . var_export($sql, true) . ";WPQUERYREQUESTBEGIN\n");


    $ids = $wpdb->get_col($sql);
    
    //$ids = array_push($ids,$nb_posts);

    highlight_string("WPQUERYREQUESTBEGIN data =\n" . var_export($nb_postis, true) . ";WPQUERYREQUESTBEGIN\n");
    if (is_array($ids) && count($ids) > 0)
        //return array_map('get_post', $ids);
        return $ids;
    else
        return []; // or whatever you like
        //return $ids;
    /* function mickeymouse(){
        global $nb_postis;
        return $nb_postis;
    }
    add_filter( 'found_posts','mickeymouse'); */
    

    highlight_string("WPQUERYREQUESTBEGIN data =\n" . var_export($garofani, true) . ";WPQUERYREQUESTBEGIN\n");
}

//add_filter( 'posts_results', 'hpp_hide_priceless_products' );
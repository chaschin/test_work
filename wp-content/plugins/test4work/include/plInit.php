<?php

/**
 * @author Alexey Chaschin
 */
 
class plInit
{
    
    public static function registerActionsAndFilters() {
        add_filter('show_admin_bar', '__return_false');
        add_action('wp_enqueue_scripts', ['plInit', 'customScriptsMethod']);

        add_filter('init', ['plInit', 'customPostTypeInit']);

        add_filter('manage_orders_posts_columns', ['plInit', 'setCustomColumns'], 10, 1);
        add_action('manage_orders_posts_custom_column', ['plInit', 'customColumns'], 10, 2);

        add_filter('archive_template', ['plInit', 'customArchiveTemplate']);
        add_filter('single_template', ['plInit', 'customPostTemplate']);
        
        add_action('init', ['plInit', 'sessionStart'], 1);
        add_action('wp_logout', ['plInit', 'endSession']);
        
        add_action('restrict_manage_posts', ['plInit', 'filterByCustomTaxonomy'] , 10, 2);
        
        add_filter('map_meta_cap', ['plInit', 'customCap'], 10, 4 );
        
        add_filter('pre_get_posts', ['plInit', 'filterPostsForCurrentAuthor']);
        add_filter('pre_get_posts', ['plInit', 'filterPostsByCustomTax']);
        add_filter('views_edit-product', ['plInit', 'removeFiltersFromEditPostPage']);
        
        add_action('admin_init', ['plInit', 'customOptionsInit']);
        add_action('admin_menu', ['plInit', 'productsArchiveOptionsPage']);
        
        add_action('wp_footer', ['plInit', 'footerScript']);
    }

    public static function sessionStart() {
        if (!session_id()) {
            session_start();
        }
    }

    public static function endSession() {
        session_destroy();
    }
    
    public function footerScript() {
        echo '
            <script>
                var admin_ajax_url = \'' . admin_url('admin-ajax.php') . '\';
            </script>
        ';
    }

    public static function customPostTemplate($single) {
        global $post;
        if ($post->post_type == 'product'){
            if (file_exists(PLUG__PLUGIN_DIR . 'templates/single_product.php')) {
                return PLUG__PLUGIN_DIR . 'templates/single_product.php';
            }
        }
        return $single;
    }

    public static function customArchiveTemplate($archive) {
        global $post;
        if (is_post_type_archive('product')){
            if (file_exists(PLUG__PLUGIN_DIR . 'templates/archive_product.php')) {
                return PLUG__PLUGIN_DIR . 'templates/archive_product.php';
            }
        }
        return $archive;
    }

    public static function setCustomColumns($columns) {
        unset($columns['author']);
        unset($columns['date']);
        $columns['title'] = __('Order ID', 'plugTranslate');
        $columns['product_title'] = __('Product title', 'plugTranslate');
        $columns['order_date'] = __('Order Date and Time', 'plugTranslate');
        $columns['client'] = __('Client', 'plugTranslate');
        return $columns;
    }

    public static function customColumns($column, $orderId) {
        $order = get_post($orderId);
        $orderData = get_post_meta($orderId, 'order_data', true);
        switch ($column) {
            case 'client' :
                $userId = $order->post_author;
                $userInfo = get_userdata($userId);
                echo get_user_meta($userId, 'first_name', true) . ' ' . get_user_meta($userId, 'last_name', true) . '<br/>';
                echo $userInfo->user_email;
                break;
            case 'product_title' :
                $product_id = get_post_meta($orderId, 'product_id', true);
                $product = get_post($product_id);
                $product_url = get_permalink($product_id);
                echo '<a href="' . $product_url . '" target="_blank">' . $product->post_title . '</a>';
                break;
            case 'order_date' :
                echo date('m.d.Y H:i', strtotime($order->post_date));
                break;
        }
    }

    public static function pluginActivation() {
        self::registerCustomTaxonomy();
        
        if (get_taxonomy('delivery_method')) {
            wp_insert_term('Pickup', 'delivery_method', ['slug' => 'pickup']);
            wp_insert_term('Mail delivery', 'delivery_method', ['slug' => 'mail_delivery']);
            wp_insert_term('Courier delivery', 'delivery_method', ['slug' => 'courier_delivery']);
        }
        
        if (get_taxonomy('status')) {
            wp_insert_term('Processed', 'status', ['slug' => 'processed']);
            wp_insert_term('Sent', 'status', ['slug' => 'sent']);
            wp_insert_term('Rejected', 'status', ['slug' => 'rejected']);
        }
        
        self::registerCustomUserRoles();
        self::setupRoleCapabilities();
    }

    public static function pluginDeactivation() {
    }

    public static function loadLanguage($domain, $langDir = 'languages/') {
        $currentLocale = get_locale();
        if (!empty($currentLocale)) {
            $moFile = THEME_DIR . '/' . $langDir . $currentLocale . ".mo";
            load_textdomain($domain, $moFile);
        }
    }

    public static function customPostTypeInit() {
    
        $labels = [
            'name'               => _x('Orders', 'post type general name', 'plugTranslate'),
            'singular_name'      => _x('Order', 'post type singular name', 'plugTranslate'),
            'menu_name'          => _x('Orders', 'admin menu', 'plugTranslate'),
            'name_admin_bar'     => _x('Order', 'add new on admin bar', 'plugTranslate'),
            'add_new'            => _x('Add New', 'book', 'plugTranslate'),
            'add_new_item'       => __('Add New Order', 'plugTranslate'),
            'new_item'           => __('New Order', 'plugTranslate'),
            'edit_item'          => __('Edit Order', 'plugTranslate'),
            'view_item'          => __('View Order', 'plugTranslate'),
            'all_items'          => __('All Orders', 'plugTranslate'),
            'search_items'       => __('Search Orders', 'plugTranslate'),
            'parent_item_colon'  => __('Parent Orders:', 'plugTranslate'),
            'not_found'          => __('No orders found.', 'plugTranslate'),
            'not_found_in_trash' => __('No orders found in Trash.', 'plugTranslate')
        ];
        $args = [
            'public'                => false,
            'labels'                => $labels,
            'publicly_queryable'    => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => false,
            'capability_type'       => 'post',
            'has_archive'           => false,
            'hierarchical'          => false,
            'menu_position'         => null,
            'supports'              => ['title'],
            'capabilities'          => [
                'create_posts'      => 'do_not_allow',
            ],
        ];
        register_post_type('orders', $args);

        $labels = [
            'name'               => _x('Products', 'post type general name', 'plugTranslate'),
            'singular_name'      => _x('Product', 'post type singular name', 'plugTranslate'),
            'menu_name'          => _x('Products', 'admin menu', 'plugTranslate'),
            'name_admin_bar'     => _x('Products', 'add new on admin bar', 'plugTranslate'),
            'add_new'            => _x('Add New', 'book', 'plugTranslate'),
            'add_new_item'       => __('Add New Product', 'plugTranslate'),
            'new_item'           => __('New Product', 'plugTranslate'),
            'edit_item'          => __('Edit Product', 'plugTranslate'),
            'view_item'          => __('View Product', 'plugTranslate'),
            'all_items'          => __('All Products', 'plugTranslate'),
            'search_items'       => __('Search Products', 'plugTranslate'),
            'parent_item_colon'  => __('Parent Product:', 'plugTranslate'),
            'not_found'          => __('No Products found.', 'plugTranslate'),
            'not_found_in_trash' => __('No Products found in Trash.', 'plugTranslate')
        ];
        $args = [
            'public'                => true,
            'labels'                => $labels,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => [
                'slug'       => 'product',
                'with_front' => false,
            ],
            'capability_type'       => 'post',
            'has_archive'           => 'products',
            'hierarchical'          => false,
            'menu_position'         => null,
            'capabilities'          => [
                'publish_posts'         => 'publish_products',
                'edit_posts'            => 'edit_products',
                'edit_others_posts'     => 'edit_others_products',
                'delete_posts'          => 'delete_products',
                'delete_others_posts'   => 'delete_others_products',
                'read_private_posts'    => 'read_private_products',
                'edit_post'             => 'edit_product',
                'delete_post'           => 'delete_product',
                'read_post'             => 'read_product',
                'edit_published_posts'  => 'edit_published_product',
                'edit_published_posts'  => 'read_product',
            ],
            'taxonomies'            => ['post_tag','category'],
            'supports'              => ['title', 'editor', 'thumbnail', 'excerpt']
        ];
        register_post_type('product', $args);
        
        self::registerCustomTaxonomy();
        self::setupRoleCapabilities();
        
    }
    
    private static function registerCustomUserRoles() {
        add_role(
            'buyer',
            __('Buyer', 'plugTranslate')
        );
        add_role(
            'seller',
            __('Seller', 'plugTranslate'),
            [
                'read'      => true,
                'level_0'   => true
            ]
        );
    }
    
    private static function setupRoleCapabilities() {
        $role = get_role('administrator');
        
        $role->add_cap('publish_products'); 
        $role->add_cap('edit_products'); 
        $role->add_cap('edit_others_products'); 
        $role->add_cap('delete_products'); 
        $role->add_cap('delete_others_products'); 
        $role->add_cap('read_private_products'); 
        $role->add_cap('edit_product'); 
        $role->add_cap('delete_product'); 
        $role->add_cap('read_product');
        
        $role = get_role('seller');
        
        $role->add_cap('publish_products'); 
        $role->add_cap('edit_products');
        $role->add_cap('edit_product'); 
        $role->add_cap('read_product');
        $role->add_cap('edit_published_product');
        
    }
    
    public static function customCap($required_caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_categories':
            case 'delete_categories':
            case 'assign_categories':
            case 'edit_post_tags':
            case 'delete_post_tags':
            case 'assign_post_tags':
                $required_caps = [
                    'edit_products',
                ];
                break;
        }
    
        return $required_caps;
    }
    
    public static function filterPostsForCurrentAuthor($query) {
        global $pagenow;
    
        if ('edit.php' != $pagenow || !$query->is_admin) {
            return $query;
        }
    
        if (!current_user_can('edit_others_posts')) {
            global $user_ID;
            $query->set('author', $user_ID);
        }
        
        return $query;
    }
    
    public static function filterPostsByCustomTax($query) {
        global $post_type, $pagenow;

        if ($pagenow == 'edit.php' && $post_type == 'orders') {
            if (isset($_GET['delivery_method']) && isset($_GET['status'])) {
                $delivery_method = sanitize_text_field($_GET['delivery_method']);
                $status = sanitize_text_field($_GET['status']);
                $args = [];
                if (!empty($delivery_method)) {
                    $args[] = [
                        'taxonomy'  => 'delivery_method',
                        'field'     => 'slug',
                        'terms'     => [$delivery_method]
                    ];
                }
                if (!empty($status)) {
                    if (count($args) > 0) {
                        $args['relation'] = 'AND';
                    }
                    $args[] = [
                        'taxonomy'  => 'status',
                        'field'     => 'slug',
                        'terms'     => [$status]
                    ];
                }
                
                if (count($args) > 0) {
                    $query->set('tax_query', $args);
                }
            }
        }   
        
        return $query;
    }
    
    public static function removeFiltersFromEditPostPage($views) {
        if (current_user_can('manage_options')) {
            return $views;
        }

        $remove_views = ['all', 'publish', 'future', 'sticky', 'draft', 'pending', 'trash'];

        foreach ($remove_views as $view) {
            if (isset($views[$view])) {
                unset($views[$view]);
            }
        }
        return $views;
    }
    
    private static function registerCustomTaxonomy() {
        $labels = array(
            'name'                       => _x('Delivery methods', 'taxonomy general name', 'plugTranslate'),
            'singular_name'              => _x('Delivery method', 'taxonomy singular name', 'plugTranslate'),
        );
        
        $args = [
            'hierarchical'          => false,
            'labels'                => $labels,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => false,
            'show_ui'               => false,
            'show_tagcloud'         => false,
            'show_in_nav_menus'     => false,
            'show_admin_column'     => true
        ];
        register_taxonomy('delivery_method', ['orders'], $args);
        
        
        $labels = array(
            'name'                       => _x('Status', 'taxonomy general name', 'plugTranslate'),
            'singular_name'              => _x('Status', 'taxonomy singular name', 'plugTranslate'),
        );
        $args = [
            'hierarchical'          => false,
            'labels'                => $labels,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => false,
            'show_ui'               => false,
            'show_tagcloud'         => false,
            'show_in_nav_menus'     => false,
            'show_admin_column'     => true
        ];
        register_taxonomy('status', ['orders'], $args);
    }
    
    public static function filterByCustomTaxonomy($postType, $which) {

        if ($postType == 'orders') {
            $taxonomies = ['delivery_method', 'status'];
        } elseif ($postType == 'product') {
            $taxonomies = ['post_tag'];
        } else {
            return;
        }


        foreach ($taxonomies as $taxonomy_slug) {
            $taxonomy_obj = get_taxonomy($taxonomy_slug);
            $taxonomy_name = $taxonomy_obj->labels->name;

            $terms = get_terms([
                'taxonomy'      => $taxonomy_slug, 
                'hide_empty'    => false
            ]);
            
            $get_filter = $taxonomy_slug;
            if ($taxonomy_slug == 'post_tag') {
                $get_filter = 'tag';
            }

            echo "<select name='{$get_filter}' id='{$get_filter}' class='postform'>";
            echo '<option value="">' . sprintf(esc_html__('Show All %s', 'plugTranslate'), $taxonomy_name) . '</option>';
            foreach ($terms as $term) {
                printf(
                    '<option value="%1$s" %2$s>%3$s</option>',
                    $term->slug,
                    ((isset($_GET[$get_filter]) && ($_GET[$get_filter] == $term->slug)) ? ' selected="selected"' : ''),
                    $term->name
                );
            }
            echo '</select>';
        }

    }

    public static function customScriptsMethod() {
        global $post;
        wp_register_script('theme', PLUG__PLUGIN_URL . 'js/theme.js', ['jquery'], '', true);

        wp_enqueue_script('jquery');
        
        $translation_array = [
            'empty_field'   => __('Field is empty', 'plugTranslate'),
            'invalid_field' => __('Invalid field data', 'plugTranslate'),
            'validated'     => __('Validated', 'plugTranslate'),
        ];
        wp_localize_script('theme', 'messages', $translation_array);
        wp_enqueue_script('theme');

    }
    
    public static function customOptionsInit() {
        register_setting('customOptions', 'products_archive_options');

        add_settings_section(
            'customOptionsSection',
            __('Product archive page settings', 'plugTranslate'),
            ['plInit', 'customOptionsSection'],
            'customOptions'
        );

        add_settings_field(
            'products_archive_text',
            __('Products archive description', 'plugTranslate'),
            ['plInit', 'customOptionsArchiveDesc'],
            'customOptions',
            'customOptionsSection',
            [
                'label_for' => 'products_archive_text',
                'class'     => 'products_archive_text_row',
            ]
        );
    }
    
    public static function customOptionsSection($args) {
        echo '<p id="' . esc_attr($args['id']) . '">' . esc_html_e('These settings will be displayed on the products archive page', 'plugTranslate') . '</p>';
    }
    
    public static function customOptionsArchiveDesc($args) {
        $options = get_option('products_archive_options');
        $name = esc_attr($args['label_for']);
        $settings = [
            'textarea_name' => 'products_archive_options[' . $name . ']'
        ];
        $content = isset($options[$name]) ? $options[$name] : '';
        wp_editor($content, $name, $settings);
    }
    
    public static function productsArchiveOptionsPage() {
        add_menu_page(
            __('Custom Options', 'plugTranslate'),
            __('Custom Options', 'plugTranslate'),
            'manage_options',
            'customOptions',
            ['plInit', 'productsArchiveOptionsPageHtml']
        );
    }
    
    public static function productsArchiveOptionsPageHtml() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_GET['settings-updated'])) {
            add_settings_error('custom_options_messages', 'custom_options_message', __('Settings Saved', 'plugTranslate'), 'updated');
        }
        
        settings_errors('custom_options_messages');

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
        echo '<form action="options.php" method="post">';
        
        settings_fields('customOptions');
        
        do_settings_sections('customOptions');
        
        submit_button('Save Settings');
        
        echo '</form>';
        echo '</div>';
    }
}

?>

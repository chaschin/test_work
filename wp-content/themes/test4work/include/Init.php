<?php

class Init
{
    protected $aviable_user_groups = [];

    public function __construct() {
        add_filter('show_admin_bar', '__return_false');

        add_action('wp_enqueue_scripts', [$this, 'customScriptsMethod']);
    }

    public function settingsUp() {
        register_nav_menus([
            'top' => 'top',
        ]);
        if (function_exists('add_theme_support')) {
            add_theme_support('post-thumbnails');
        }
        if (function_exists('add_image_size')) {
            add_image_size('ico', 50, 50, true);
            add_image_size('blog-preview', 450, 450, true);
        }
    }

    public function customScriptsMethod() {
        
        $current_theme = wp_get_theme();
        $ver = $current_theme->get('Version');
        
        wp_register_style('style', THEME_URL . '/style.css', ['bootstrap'], $ver);
        wp_register_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css', [], '4.0.0');
        
        wp_deregister_script('jquery');
        wp_deregister_script('jquery-migrate');
        wp_register_script('jquery', includes_url('/js/jquery/jquery.js'), false, NULL, true);
        wp_register_script('jquery-migrate', includes_url('/js/jquery/jquery-migrate.min.js'), array('jquery'), NULL, true);
        
        wp_register_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', ['jquery'], '4.0.0', true);
        wp_register_script('slim', 'https://code.jquery.com/jquery-3.2.1.slim.min.js', ['jquery'], '3.2.1', true);
        wp_register_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js', ['jquery'], '1.12.9', true);
        
        if (!is_admin()) {
            wp_enqueue_style('style');
            wp_enqueue_style('bootstrap');
            
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-migrate');
            wp_enqueue_script('bootstrap-js');
            wp_enqueue_script('slim');
            wp_enqueue_script('popper');
        }
    }
}

?>

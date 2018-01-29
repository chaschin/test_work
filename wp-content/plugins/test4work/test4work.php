<?php
/*
 * @package test4work
 *
 *
 * Plugin Name: test4work
 * Plugin URI: 
 * Description: 
 * Version: 1.0
 * Author: Alexey Chaschin
 * Author URI: 
 * Text Domain: plugTranslate
 *
 */
 
    if (!function_exists('add_action')) {
        echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
        exit;
    }

    define('PLUG__PLUGIN_DIR', plugin_dir_path(__FILE__));
    define('PLUG__PLUGIN_URL', plugin_dir_url(__FILE__));

    function plugAutoloader($class_name) {
        $status = false;
        $include_path = '';
        $pathes = array(
            PLUG__PLUGIN_DIR . 'include/',
        );
        foreach ($pathes as $path) {
            $filename = $class_name . '.php';
            $file = $path . $filename;
            if (file_exists($file) != false) {
                $include_path = $file;
                $status = true;
            }
        }
        if ($status) {
            include_once $include_path;
        } else {
            return false;
        }
    }

    spl_autoload_register('plugAutoloader');
    
//     register_activation_hook(__FILE__, array('plInit', 'pluginActivation'));
//     register_deactivation_hook(__FILE__, array('plInit', 'pluginDeactivation'));

//     add_action('init', array('plInit', 'get_instance'));
//     add_action('init', array('plInit', 'sessionStart'), 1);
//     add_action('wp_logout', array('plInit', 'endSession'));
//     add_action('plugins_loaded', array('pageTemplater', 'get_instance'));

    register_activation_hook(__FILE__, ['plInit', 'pluginActivation']);
    register_deactivation_hook(__FILE__, ['plInit', 'pluginDeactivation']);

    plInit::registerActionsAndFilters();
    
    $generalAjax = new generalAjax();

?>
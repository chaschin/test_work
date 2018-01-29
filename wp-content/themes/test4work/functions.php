<?php
/**
 * test4work functions and definitions
 *
 * @package WordPress
 * @subpackage test4work
 * @since test4work 1.0
 *
 */

define('THEME_URL', get_template_directory_uri());
define('THEME_DIR', WP_CONTENT_DIR . '/themes/' . get_option('template'));

define('DOMAIN', 'test4work');

function themeAutoloader($class_name) {
    $status = false;
    $include_path = '';
    $pathes = array(
        THEME_DIR . '/include/',
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

spl_autoload_register('themeAutoloader');

$init = new Init();
$init->settingsUp();
// $init->loadLanguage('skysaver');

function getTranslate($text) {
    return __($text, DOMAIN);
}

function echoTranslate($text) {
    _e($text, DOMAIN);
}

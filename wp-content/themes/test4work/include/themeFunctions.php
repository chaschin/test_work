<?php

class themeFunctions
{
    public static function generateIerarhicalMenu($menuSlug) {
        $home = get_option('home');
        $url = $home . $_SERVER['REQUEST_URI'];
        $locations = get_nav_menu_locations();
        $menu = wp_get_nav_menu_object($locations[$menuSlug]);
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        $menu_a = [];
        $menu_keys = [];
        $i = 0;
        if ($menu_items) {
            foreach ($menu_items as $m) {
                if ($m->menu_item_parent == 0) {
                    $menu_a[$i]['title'] = $m->title;
                    $menu_a[$i]['url'] = $m->url;
                    $menu_a[$i]['object'] = $m->object;
                    $menu_a[$i]['id'] = $m->object_id;
                    if ($url == $m->url) {
                        $menu_a[$i]['current'] = true;
                    } else {
                        $menu_a[$i]['current'] = false;
                    }
                    $menu_keys[$m->ID] = &$menu_a[$i];
                    $i ++;
                } else {
                    if (isset($menu_keys[$m->menu_item_parent]['childs'])) {
                        $c = count($menu_keys[$m->menu_item_parent]['childs']);
                    } else {
                        $c = 0;
                    }
                    $current = false;
                    if ($url == $m->url) {
                        $current = true;
                    }
                    $menu_keys[$m->menu_item_parent]['childs'][$c] = array(
                        'title'     => $m->title,
                        'url'       => $m->url,
                        'object'    => $m->object,
                        'id'        => $m->object_id,
                        'current'   => $current
                    );
                    $menu_keys[$m->ID] = &$menu_keys[$m->menu_item_parent]['childs'][$c];
                }
            }
        }
        return $menu_a;
    }
}

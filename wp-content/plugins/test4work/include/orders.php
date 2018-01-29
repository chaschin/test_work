<?php

/**
 * @author Alexey Chaschin
 */
class orders
{
    
    public static function createOrder($data) {
        $key = uniqid('order-');
        $options = array(
            'post_title'    => $key,
            'post_type'     => 'orders',
            'post_status'   => 'publish',
            'post_author'   => $data['user_id']
        );
        $orderId = wp_insert_post($options);
        if ($orderId) {
            update_post_meta($orderId, 'product_id', $data['product_id']);
            
            $status = get_term_by('slug', 'sent', 'status');
            wp_set_post_terms($orderId, [$status->term_id], 'status');
            wp_set_post_terms($orderId, [$data['delivery_method']], 'delivery_method');
            
        }
        return $orderId;
    }
    
}

?>

<?php

    class generalAjax
    {
        function __construct() {
            add_action('wp_ajax_nopriv_create_order', array($this, 'createOrder'));
            add_action('wp_ajax_create_order', array($this, 'createOrder'));
        }
        
        public function createOrder() {
            $result = [];
            $result['status'] = true;
            $data = [];
            
            // first_name, last_name, email, delivery_method
            
            if (empty($_POST['first_name'])) {
                $result['errors']['first_name'] = __('Field is empty', 'plugTranslate');
                $result['status'] = false;
            } elseif (!preg_match('/^[a-zA-Z \.]{2,16}$/', $_POST['first_name'])) {
                $result['errors']['first_name'] = __('Invalid First Name', 'plugTranslate');
                $result['status'] = false;
            } else {
                $data['first_name'] = plFunctions::escapeQuery($_POST['first_name']);
            }
            
            if (empty($_POST['last_name'])) {
                $result['errors']['last_name'] = __('Field is empty', 'plugTranslate');
                $result['status'] = false;
            } elseif (!preg_match('/^[a-zA-Z \.]{2,16}$/', $_POST['last_name'])) {
                $result['errors']['last_name'] = __('Invalid Last Name', 'plugTranslate');
                $result['status'] = false;
            } else {
                $data['last_name'] = plFunctions::escapeQuery($_POST['last_name']);
            }
            
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $user_data = get_userdata($user_id);
                
                $data['email'] = $user_data->user_email;
                $data['user_id'] = $user_id;
            } else {
                if (empty($_POST['email'])) {
                    $result['errors']['email'] = __('Field is empty', 'plugTranslate');
                    $result['status'] = false;
                } else {
                    $user_email = apply_filters('user_registration_email', $_POST['email']);
                    if (!is_email($user_email)) {
                        $result['errors']['email'] = __('Invalid email', 'plugTranslate');
                        $result['status'] = false;
                    } elseif (email_exists($user_email)) {
                        $result['errors']['email'] = __('This email already exists', 'plugTranslate');
                        $result['status'] = false;
                    } else {
                        $user_pass = wp_generate_password(12,false);
                        $user_id = wp_create_user($user_email, $user_pass, $user_email);
                        if ($user_id instanceof WP_Error) {
                            $result['errors']['email'] = __('User creating error', 'plugTranslate');
                            $result['status'] = false;
                        } else {
                            $data['user_id'] = $user_id;
                            $data['email'] = $user_email;
                            wp_update_user([
                                'ID'   => $data['user_id'], 
                                'role' => 'buyer',
                            ]);
                        }
                    }
                }
            }
            
            $checkDelivery = [];
            $delivery_methods = get_terms([
                'taxonomy' => 'delivery_method',
                'hide_empty' => false,
            ]);
            foreach ($delivery_methods as $dm) {
                $checkDelivery[] = $dm->term_id;
            }
            
            if (!in_array(intval($_POST['delivery_method']), $checkDelivery)) {
                $result['errors']['delivery_method'] = __('Wrong delivery', 'plugTranslate');
                $result['status'] = false;
            } else {
                $data['delivery_method'] = intval($_POST['delivery_method']);
            }
            
            if ($result['status']) {
                wp_update_user([
                    'ID'         => $data['user_id'], 
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'] 
                ]);
                $data['product_id'] = intval($_POST['product_id']);
                $order_id = orders::createOrder($data);
                $result['status_message'] = __('Your order created', 'plugTranslate');
            } else {
                $result['status_message'] = __('Errors founds', 'plugTranslate');
            }
            
            echo json_encode($result);
            die();
        }

    }

?>
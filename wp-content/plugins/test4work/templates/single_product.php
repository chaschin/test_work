<?php
    get_header();
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            $product_id = $post->ID;
?>
<div class="container">
    <h2><?php the_title(); ?></h2>
    <br/>
    <input class="product_buy_btn" value="<?php _e('Buy', 'plugTranslate'); ?>" data-product-id="<?php echo $product_id; ?>" type="button" />
    <br/>
    <br/>
    <a href="<?php echo get_post_type_archive_link('product'); ?>"><?php _e('Products archive page', 'plugTranslate'); ?></a>
</div>

<div class="modal fade" id="orderFormModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <?php
        $first_name = '';
        $last_name = '';
        $email = '';
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $user_data = get_userdata($user_id);
            $first_name = $user_data->first_name;
            $last_name = $user_data->last_name;
            $email = $user_data->user_email;
        }
        $delivery_methods = get_terms([
            'taxonomy' => 'delivery_method',
            'hide_empty' => false,
        ]);
        $delivery_options = '';
        foreach ($delivery_methods as $dm) {
            $delivery_options .= '<option value="' . $dm->term_id . '">' . $dm->name . '</option>';
        }
    ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="orderFormModalLabel"><?php _e('Order form for', 'plugTranslate'); ?> '<span><?php the_title(); ?></span>'</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="orderForm" action="/" method="post">
                <input type="hidden" name="action" value="create_order" />
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
                <div class="form-group">
                    <label for="first_name" class="col-form-label"><?php _e('First Name', 'plugTranslate'); ?></label>
                    <input type="text" name="first_name" class="form-control need2validate validate_name" id="first_name" value="<?php echo $first_name; ?>" />
                </div>
                <div class="form-group">
                    <label for="last_name" class="col-form-label"><?php _e('Last Name', 'plugTranslate'); ?></label>
                    <input type="text" name="last_name" class="form-control need2validate validate_name" id="last_name" value="<?php echo $last_name; ?>" />
                </div>
                <div class="form-group">
                    <label for="email" class="col-form-label"><?php _e('Email', 'plugTranslate'); ?></label>
                    <input type="email" name="email" class="form-control need2validate validate_email" id="email" value="<?php echo $email; ?>" />
                </div>
                <div class="form-group">
                    <label for="delivery_method" class="col-form-label"><?php _e('Delivery method', 'plugTranslate'); ?></label>
                    <select name="delivery_method" class="form-control" id="delivery_method"><?php echo $delivery_options; ?></select>
                </div>
            </form>
            <div id="ajax-results" class="alert-area"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary order-action" data-dismiss="modal"><?php _e('Cancel', 'plugTranslate'); ?></button>
            <button type="button" class="btn btn-primary order-action submit-order"><?php _e('Submit', 'plugTranslate'); ?></button>
            <button type="button" class="btn btn-primary close-action" data-dismiss="modal" style="display: none;"><?php _e('Close', 'plugTranslate'); ?></button>
        </div>
        </div>
    </div>
</div>

<?php
        endwhile;
    endif;
?>

<?php
    get_footer();
?>

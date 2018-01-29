<?php
    get_header();
?>
<div class="container">
    <h1><?php _e('Products', 'plugTranslate'); ?></h1>
    <?php
        $desc = get_option('products_archive_options');
        if (!empty($desc['products_archive_text'])) {
            echo '<p>' . $desc['products_archive_text'] . '</p>';
        }
    ?>
    <div class="row">
        <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    $product_link = get_permalink();
        ?>
            <div class="col-md-4">
                <h2><a href="<?php echo $product_link; ?>"><?php the_title(); ?></a></h2>
                <br/>
                <a href="<?php echo $product_link; ?>" class="btn btn-primary"><?php _e('Buy', 'plugTranslate'); ?></a>
            </div>
        <?php
                endwhile;
            endif;
        ?>
    </div>
</div>
<?php
    get_footer();
?>
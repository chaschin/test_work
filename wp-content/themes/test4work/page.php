<?php
    get_header();
    if (have_posts()) {
        while (have_posts()) {
            the_post();
?>

<div class="container">
    <div class="row justify-content-md-center">
        <div class="col col-lg-12">
            <h2 class="text-center"><?php the_title(); ?></h2>
            <?php the_content(); ?>
        </div>
    </div>
</div>


<?php
        }
    }
    get_footer();
?>

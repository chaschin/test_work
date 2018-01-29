<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=0"/>
<title><?php
    wp_title('|', true, 'right');
    bloginfo('name');
?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="<?php echo get_option('home'); ?>">Test4work</a>
                <ul class="nav">
                <?php
                    $menu = themeFunctions::generateIerarhicalMenu('top');
                    foreach ($menu as $m) {
                        echo '<li class="nav-item"><a href="' . $m['url'] . '">' . $m['title'] . '</a></li>';
                    }
                ?>
                </ul>
            </div>
        </nav>
    </header>
    <br/>
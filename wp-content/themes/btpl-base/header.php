<!doctype html>
<html id="html">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
    <link rel="shortcut icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/favicon.png" />
    <link rel="favicon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.png">

    <?php get_template_part( "template-parts/head-tag"); ?>

    <?php wp_head(); ?>

</head>

<body <?php body_class(); // Standard WP body classes ?>>
    <span id="top-anchor"></span>
    <div id="root">

        <div id="top">
            <div class="content-max-width">
                <?php get_template_part( "template-parts/header/header", "primary" ); ?>
            </div>
        </div>


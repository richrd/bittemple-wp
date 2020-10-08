<?php

add_action( 'wp_enqueue_scripts', function () {
    #                  handle            url
    wp_register_script('btpl_canvas_fx', get_stylesheet_directory_uri() . '/assets/js/canvas-fx.js', [], '1.0', true);
    wp_enqueue_script('btpl_canvas_fx');

    // Main JS
    wp_register_script('btpl_main', get_stylesheet_directory_uri() . '/assets/js/main.js', ['btpl_canvas_fx'], '1.0', true);
    wp_enqueue_script('btpl_main');

    // Fonts
    wp_enqueue_style('btpl_fonts_miso', get_stylesheet_directory_uri() . '/assets/fonts/miso/miso.css', false, '1.0.0');
    wp_enqueue_style('btpl_fonts_miso_light', get_stylesheet_directory_uri() . '/assets/fonts/miso/miso-light.css', false, '1.0.0');
    wp_enqueue_style('btpl_fonts_dinot_regular', get_stylesheet_directory_uri() . '/assets/fonts/dinot/dinot-regular.css', false, '1.0.0');
});

add_action( 'admin_enqueue_scripts', function () {
    // Fonts
    wp_enqueue_style('btpl_fonts_miso', get_stylesheet_directory_uri() . '/assets/fonts/miso/miso.css', false, '1.0.0');
    wp_enqueue_style('btpl_fonts_miso_light', get_stylesheet_directory_uri() . '/assets/fonts/miso/miso-light.css', false, '1.0.0');
    wp_enqueue_style('btpl_fonts_dinot_regular', get_stylesheet_directory_uri() . '/assets/fonts/dinot/dinot-regular.css', false, '1.0.0');
});

// Editor styles
//add_theme_support( 'dark-editor-style' );
add_editor_style( 'assets/css/gutenberg-child.css' );

add_action('after_setup_theme', function () {
    btpl_base_theme()->add_theme_colors([
        // Default color override, name not required
        [
            'slug' => 'page-foreground',
            'color' => '#bbb',
        ],
        [
            'slug' => 'page-background',
            'color' => '#161616',
        ],
        // Custom colors
        [
            'name' => __( 'Page Foreground Accent 1', 'btpl_clihd' ),
            'slug' => 'page-foreground-accent-1',
            'color' => '#1c1c1c',
        ],
        [
            'name' => __( 'Page Foreground Accent 2', 'btpl_clihd' ),
            'slug' => 'page-foreground-accent-2',
            'color' => '#191919',
        ],
        [
            'name' => __( 'Bittemple Green 1', 'btpl_clihd' ),
            'slug' => 'bittemple-green-1',
            'color' => '#9f9',
        ],
        [
            'name' => __( 'Bittemple Green 1 Shadow', 'btpl_clihd' ),
            'slug' => 'bittemple-green-1-shadow',
            'color' => 'rgba(79, 248, 208, 0.64)',
        ],
        [
            'name' => __( 'Bittemple Yellow 1', 'btpl_clihd' ),
            'slug' => 'bittemple-yellow-1',
            'color' => '#fc6',
        ],
    ]);
}, 1100);

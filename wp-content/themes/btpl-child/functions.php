<?php

add_action( 'wp_enqueue_scripts', function () {
        #                  handle            url
        wp_register_script('btpl_canvas_fx', get_stylesheet_directory_uri() . '/assets/js/canvas-fx.js', [], '1.0', true);
        wp_enqueue_script('btpl_canvas_fx');

        wp_register_script('btpl_main', get_stylesheet_directory_uri() . '/assets/js/main.js', ['btpl_canvas_fx'], '1.0', true);
        wp_enqueue_script('btpl_main');

    }
);

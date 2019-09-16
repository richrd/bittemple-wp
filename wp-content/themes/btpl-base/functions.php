<?php

add_action('after_setup_theme', function () {
    /*
     * Encapsulate everything in an anonymous class to
     * avoid polluting the global namespace.
     */
    new class() {
        public function __construct() {
            // Let WordPress manage the document title tag.
            add_theme_support('title-tag');

            // Featured image support
            add_theme_support('post-thumbnails');

            // Enqueue scripts and styles
            add_action('wp_enqueue_scripts', [$this, 'enqueue_stylesheets']);

            // Switch default core markup for search form, comment form, and comments
            // to output valid HTML5.
            add_theme_support('html5', array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ));
          }

        public function enqueue_stylesheets() {
            // TODO: possibly use local fontawesome
            wp_enqueue_style('font-awesome', '//use.fontawesome.com/releases/v5.0.7/css/all.css');
        }
    };
});

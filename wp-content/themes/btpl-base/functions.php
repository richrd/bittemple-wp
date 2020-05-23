<?php

add_action('after_setup_theme', function () {
    /*
     * Encapsulate everything in an anonymous class to
     * avoid polluting the global namespace.
     */
    new class() {
        public function __construct() {
            $this->setup_theme_support();

            // Enqueue scripts and styles
            add_action('wp_enqueue_scripts', [$this, 'enqueue_stylesheets']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_stylesheets']);

            // Register Menus
            register_nav_menu('top-bar-menu', __( 'Top Bar Menu' ));
            register_nav_menu('main-menu', __( 'Main Menu' ));

            // Setup theme customizer
            add_action('customize_register', [$this, 'setup_customizer']);
        }

        private function _namespace($str) {
            // Theme slug + variable name
            return wp_get_theme()->template . '_' . $str;
        }

        public function get_setting_value($name) {
            $original_value = get_theme_mod($this->_namespace($name));
            if ($original_value) {
                return $original_value;
            }

            return $this->setting_defaults[$name];
        }

        private function setup_theme_support() {
            // Let WordPress manage the document title tag.
            add_theme_support('title-tag');

            // Featured image support
            add_theme_support('post-thumbnails');

            // Enable wide and full width alignment in Gutenberg
            add_theme_support('align-wide');

            // Switch default core markup for search form, comment form, and comments
            // to output valid HTML5.
            add_theme_support('html5', array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ));

            // Allow setting custom logo
            $customLogoDefaults = array(
                'height'      => 100,
                'width'       => 400,
                'flex-height' => true,
                'flex-width'  => true,
                'header-text' => array(
                    'site-title',
                    'site-description'
                ),
            );
            add_theme_support('custom-logo', $customLogoDefaults);
            add_filter('excerpt_length', [$this, 'filter_excerpt_length']);
            add_filter('excerpt_more', [$this, 'filter_excerpt_more']);

        }

        public function enqueue_stylesheets() {
            // Silence is quiet.
        }

        public function enqueue_admin_stylesheets() {
            // TODO: use or remove
            // wp_enqueue_style('admin_css', get_template_directory_uri() . '/assets/css/admin.css', false, '1.0.0');
        }

        public function setup_customizer($wp_customize) {
            // Add theme options section
            $wp_customize->add_section($this->_namespace('theme_settings'), array(
                'title' => __('Theme Options'),
                'priority' => 190,
                'description' => '',
            ));

            // Excerpt length
            $wp_customize->add_setting($this->_namespace('excerpt_length'), array(
                  'type' => 'theme_mod',
                  'capability' => 'edit_theme_options',
                  'default' => '', // Default value
                  'transport' => 'refresh', // or postMessage
            ));
            $wp_customize->add_control($this->_namespace('excerpt_length'), array(
                  'type' => 'number',
                  'priority' => 1,
                  'section' => $this->_namespace('theme_settings'),
                  'label' => __( 'Excerpt Length' ),
                  'description' => __( 'Maximum number of words shown in excerpts.' ),
            ));

            // Excerpt ending
            $wp_customize->add_setting($this->_namespace('excerpt_more'), array(
                  'type' => 'theme_mod',
                  'capability' => 'edit_theme_options',
                  'default' => '', // Default value
                  'transport' => 'refresh', // or postMessage
            ));
            $wp_customize->add_control($this->_namespace('excerpt_more'), array(
                  'type' => 'text',
                  'priority' => 1,
                  'section' => $this->_namespace('theme_settings'),
                  'label' => __( 'Excerpt Ending' ),
                  'description' => __( 'The cut off marker at the end of excerpts. Default: [...]' ),
                  'input_attrs' => array(
                      'placeholder' => ' [...]',
                  ),
            ));
        }

        public function filter_excerpt_length($default) {
            $setting_value = get_theme_mod($this->_namespace('excerpt_length'));
            return $setting_value ? $setting_value : $default;
        }

        public function filter_excerpt_more($default) {
            $setting_value = get_theme_mod($this->_namespace('excerpt_more'));
            return $setting_value ? $setting_value : $default;
        }
    };
});

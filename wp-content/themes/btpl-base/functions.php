<?php

add_action('after_setup_theme', function () {
    /*
     * Encapsulate everything in an anonymous class to
     * avoid polluting the global namespace.
     */
    $theme = new class() {
        public function __construct() {
            // Load translations
            load_theme_textdomain( 'btpl-base', get_stylesheet_directory() . '/languages' );

            // Theme color palette.
            // The colors can be used within the Gutenberg editor
            // as well as with CSS variables in the child theme.
            // These colors can be added and overriden via the child
            // theme.
            $this->color_palette = [
                [
                    'name' => __( 'Page Background', 'btpl-base' ),
                    'slug' => 'page-background',
                    'color' => '#fff',
                ],
                [
                    'name' => __( 'Page Foreground', 'btpl-base' ),
                    'slug' => 'page-foreground',
                    'color' => '#0f0',
                ],
                [
                    'name' => __( 'Page Foreground Muted', 'btpl-base' ),
                    'slug' => 'page-foreground-muted',
                    'color' => '#777',
                ],
            ];

            // Initialize colors
            add_theme_support( 'editor-color-palette', $this->color_palette);


            $this->setting_defaults = [
                "excerpt_length" => "",
                "excerpt_more" => "",
                "max_footer_columns" => 3,
            ];

            $this->setup_theme_support();
            $this->setup_sidebars();

            // Enqueue scripts and styles
            add_theme_support( 'editor-styles' );
            add_editor_style( 'assets/css/gutenberg.css' );
            add_action('wp_enqueue_scripts', [$this, 'enqueue_stylesheets']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_stylesheets']);

            // Enqueue generated <style> tag with CSS variables etc
            add_action('wp_head', [$this, 'enqueue_inline_styles']);
            add_action('admin_head', [$this, 'enqueue_admin_inline_styles']);

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

            if ( array_key_exists( $name, $this->setting_defaults ) ) {
                return $this->setting_defaults[$name];
            }

            return null;
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

        public function add_theme_colors($colors) {
            foreach ($colors as $color) {
                $this->add_theme_color($color);
            }
        }

        public function add_theme_color($color) {
            // Add a new theme color or update an existing one
            foreach ($this->color_palette as $key => $existingColor) {
                if ($color['slug'] == $existingColor['slug']) {

                    if ( array_key_exists ( 'color', $color ) ) {
                        $existingColor['color'] = $color['color'];
                    }

                    if ( array_key_exists ( 'name', $color ) ) {
                        $existingColor['name'] = $color['name'];
                    }

                    $this->color_palette[$key] = $existingColor;
                    add_theme_support( 'editor-color-palette', $this->color_palette);
                    return;
                }
            }

            $this->color_palette[] = $color;
            add_theme_support( 'editor-color-palette', $this->color_palette);
        }

        public function enqueue_inline_styles() {
            // Output theme colors as CSS variables and `.has-*-color` classes
            echo $this->generate_color_styles();
        }

        public function enqueue_admin_inline_styles() {
            // Output theme colors as CSS variables and `.has-*-color` classes
            echo $this->generate_color_styles();
        }

        public function generate_color_styles() {
            // Output theme colors as CSS variables and `.has-*-color` classes
            $variables = ":root {\n";
            $classes = "";
            foreach ($this->color_palette as $color) {
                // Override default if the color is defined in theme settings
                $override = $this->get_setting_value('theme-color-' . $color['slug']);
                $value = $override ? $override : $color['color'];

                if (!$value) {
                    continue;
                }

                $variables .= '    --theme-color-' . $color['slug'] . ': ' . $value . ";\n";
                $classes .= '.has-' . $color['slug'] . '-color{color: var(--theme-color-' . $color['slug'] . ")}\n";
            }
            $variables .= "}";

            return "<style>\n$variables\n$classes</style>\n";
        }

        private function setup_sidebars() {
            // Define and register sidebars
            $widget_areas = array(
                // Top Bar
                array(
                    'name'          => 'Top Bar 1',
                    'id'            => 'top-bar-1',
                    'description'   => 'First widget area in the bar at the top of the site. ',
                ),
                array(
                    'name'          => 'Top Bar 2',
                    'id'            => 'top-bar-2',
                    'description'   => 'Second widget area in the bar at the top of the site.',
                ),

                // Sidebars
                array(
                    'name'          => 'Main Sidebar Left',
                    'id'            => 'main-sidebar-left',
                    'description'   => '',
                ),
                array(
                    'name'          => 'Main Sidebar Right',
                    'id'            => 'main-sidebar-right',
                    'description'   => '',
                ),

                // Footer
                array(
                    'name'          => 'Footer Top Row',
                    'id'            => 'footer-top-row',
                    'description'   => 'Top row of the footer.'
                ),
                array(
                    'name'          => 'Footer Column 1',
                    'id'            => 'footer-column-1',
                    'description'   => 'Column 1 of the footer widget columns.'
                ),
                array(
                    'name'          => 'Footer Column 2',
                    'id'            => 'footer-column-2',
                    'description'   => 'Column 2 of the footer widget columns.'
                ),
                array(
                    'name'          => 'Footer Column 3',
                    'id'            => 'footer-column-3',
                    'description'   => 'Column 3 of the footer widget columns.'
                ),
                array(
                    'name'          => 'Footer Column 4',
                    'id'            => 'footer-column-4',
                    'description'   => 'Column 4 of the footer widget columns.'
                ),
                array(
                    'name'          => 'Footer Column 5',
                    'id'            => 'footer-column-5',
                    'description'   => 'Column 5 of the footer widget columns.'
                ),
                array(
                    'name'          => 'Footer Column 6',
                    'id'            => 'footer-column-6',
                    'description'   => 'Column 6 of the footer widget columns.'
                ),
                array(
                    'name'          => 'Footer Bottom Row',
                    'id'            => 'footer-bottom-row',
                    'description'   => 'Bottom row of the footer.'
                ),
            );

            foreach ($widget_areas as $widget_area) {
                $sidebar_config = $widget_area;
                $sidebar_config['before_widget'] = '<div class="widget %2$s">';
                $sidebar_config['after_widget'] = '</div>';
                $sidebar_config['before_title'] = '<h2 class="widget-title">';
                $sidebar_config['after_title'] = '</h2>';
                register_sidebar($sidebar_config);
            }
        }

        public function enqueue_stylesheets() {
            // Silence is quiet.
        }

        public function enqueue_admin_stylesheets() {
            // TODO: use or remove
            // wp_enqueue_style('admin_css', get_template_directory_uri() . '/assets/css/admin.css', false, '1.0.0');
        }

        public function setup_customizer($wp_customize) {
            // Custom color picker
            require_once("lib/alpha-color-picker/alpha-color-picker.php");

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

            // Make theme colors editable
            foreach ($this->color_palette as $color) {
                $setting_name = $this->_namespace('theme-color-' . $color['slug']);
                $wp_customize->add_setting($setting_name, array(
                      'type' => 'theme_mod',
                      'capability' => 'edit_theme_options',
                      'default' => $color['color'], // Default value
                      'transport' => 'refresh', // or postMessage
                ));
                $wp_customize->add_control(
                    new Customize_Alpha_Color_Control($wp_customize, $setting_name,
                        array(
                            'label'      => $color['name'],
                            'section'    => 'colors',
                            'settings'   => $setting_name,
                        )
                    )
                );
            }
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

    // Custom nav walkers for nested sub menus
    // Adds a wrapper div to sub menus for better styling support
    class Walker_Nav_Menu_Wrapped extends Walker_Nav_Menu {
        function start_lvl( &$output, $depth = 0, $args = array() )
        {
            $output .= "\n<div class=\"sub-menu-wrapper\">\n";
            $output .= "\n<ul class=\"sub-menu\">\n";
        }

        function end_lvl( &$output, $depth = 0, $args = array() )
        {
            $output .= "\n</ul>\n";
            $output .= "\n</div>\n";
        }
    }

    class Walker_Page_Wrapped extends Walker_Page {
        function start_lvl( &$output, $depth = 0, $args = array() )
        {
            $output .= "\n<div class=\"sub-menu-wrapper\">\n";
            $output .= "\n<ul class=\"sub-menu\">\n";
        }

        function end_lvl( &$output, $depth = 0, $args = array() )
        {
            $output .= "\n</ul>\n";
            $output .= "\n</div>\n";
        }
    }


    // Expose the class to the theme
    global $btpl_base_theme;
    $btpl_base_theme = $theme;
    function btpl_base_theme() {
        global $btpl_base_theme;
        return $btpl_base_theme;
    }
}, 999);

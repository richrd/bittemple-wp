<?php

class BittempleBaseThemeFunctions {
	function __construct() {
		add_action('after_setup_theme', array($this, 'init'));
		// Register action/filter callbacks here...
	}

    function init() {
		// Remove xmlrpc url from head
		add_filter('xmlrpc_enabled', '__return_false');

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

	function enqueue_stylesheets() {
		wp_enqueue_style('font-awesome', '//use.fontawesome.com/releases/v5.0.7/css/all.css'); 
	}

	// TODO: Use or remove this
	function custom_excerpt_length () {
		return 100;
	}
}

$bittemple_base_functions = new BittempleBaseThemeFunctions();

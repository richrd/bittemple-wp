<?php
/*
   Plugin Name: Bittemple WP Harden
   Plugin URI: http://example.com
   description: Disable unneeded WP features
   Version: 0.1
   Author: Richard Lewis
   Author URI: http://bittemple.org
   License: UNLICENSED
*/


 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


add_action( 'wp_loaded', function () {
    /*
     * Encapsulate everything in an anonymous class to
     * avoid polluting the global namespace.
     */
    new class() {
        public function __construct() {
            $this->cleanup_head_contents();
            $this->disable_xmlrpc();
            $this->remove_dns_prefetch();
            $this->block_enumaration();
            $this->hide_wp_version();
        }


        /**
         * Disable unneeded markup in <head>
         */
        public function cleanup_head_contents() {
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
        }


        /**
         * Disable XML RPC
         */
        public function disable_xmlrpc() {
            add_filter('xmlrpc_enabled', '__return_false');
            remove_action ('wp_head', 'rsd_link');
        }


        /**
         * Disable DNS prefetch tags in <head>
         */
        public function remove_dns_prefetch() {
           remove_action('wp_head', 'wp_resource_hints', 2, 99);
        }


        /*
         * Block enumerating usernames (using e.g. wp-scan to scan wordpress usernames)
         */
        public function block_enumaration() {
            if (!is_admin()) {
                if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) die();
                add_filter('redirect_canonical', [$this, 'block_enumaration_check'], 10, 2);
            }
        }

        public function block_enumaration_check($redirect, $request) {
            if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) {
                die();
            } else {
                return $redirect;
            }
        }


        /*
         * Hide WP version strings from scripts, styles rss feeds etc.
         * NOTE: WP-Scan still detects the version by using advanced
         *       file fingerprinting with md5 hashes.
         */
        public function hide_wp_version() {
            add_filter('script_loader_src', [$this, 'remove_url_version_strings']);
            add_filter('style_loader_src', [$this, 'remove_url_version_strings']);
            add_filter('the_generator', [$this, 'remove_wordpress_version']);
        }

        public function hide_the_generator() {
            return '';
        }

        public function remove_url_version_strings($src) {
            global $wp_version;

            $parts = explode( '?', $src );

            if (count($parts) < 2) {
                return $src;
            }

            if ($parts[1] === 'ver=' . $wp_version) {
                // Replace the version with a CRC to retain
                // the cache busting functionality.
                return $parts[0] . '?ver=' . crc32(strrev($wp_version));
            }

            return $src;
        }

    };
});



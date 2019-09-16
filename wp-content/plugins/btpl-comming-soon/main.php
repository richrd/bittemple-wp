<?php
/**
 * Plugin Name:       Bittemple Minimalist Comming Soon Page
 * description:       Minimalist coming soon page.
 * Version:           0.0.1
 * Author:            Richard Lewis
 * Author URI:        http://bittemple.org
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


(function () {
    $btpl_run_comming_soon = function() {
        // Don't block logged in users or users trying to log in
        if (is_user_logged_in() || is_admin() || $GLOBALS['pagenow'] === 'wp-login.php') {
            return;
        }

        echo "woah! what are you here for? i'm not ready yet!";
        die();
    };

    add_action('muplugins_loaded', $btpl_run_comming_soon);
    add_action('plugins_loaded', $btpl_run_comming_soon);
})();

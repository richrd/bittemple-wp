<?php
/*
   Plugin Name: Minimalist Comming Soon Page
   Plugin URI: http://example.com
   description: Minimalist Comming Soon Page
   Version: 0.1
   Author: Richard Lewis
   Author URI: http://bittemple.org
   License: UNLICENSED
*/

function btpl_run_comming_soon() {
    // Don't block logged in users or users trying to log in
    if (is_user_logged_in() || is_admin() || $GLOBALS['pagenow'] === 'wp-login.php') {
        return;
    }

    echo "woah! what are you here for? i'm not ready yet!";
    die();
}


add_action('muplugins_loaded', 'btpl_run_comming_soon');
add_action('plugins_loaded', 'btpl_run_comming_soon');



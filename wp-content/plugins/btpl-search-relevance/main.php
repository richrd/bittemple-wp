<?php
/**
 * @link              http://example.com
 * @since             1.0.0
 * @package           Bittemple_Search_Relevance
 *
 * @wordpress-plugin
 * Plugin Name:       Bittemple Search Relevance
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       TODO: This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Richard Lewis
 * Author URI:        http://example.com/
 * License:           MIT
 * License URI:       TODO
 * Text Domain:       btpl-search-relevance
 * Domain Path:       /languages
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

class Bittemple_Search_Relevance {
    function init() {
        add_filter('posts_clauses', [$this, 'search_add_keyword_count']);
        add_filter('posts_orderby', [$this, 'search_order_by_keyword_count'], 10, 2);
    }

    /*
    * Improve WordPress searh by ordering the results by relevance.
    * Relevance is determined by counting the occurences of the search term in the post content.
    *
    * NOTE: this works best with a single keyword search
    */
    function get_search_keyword_fields() {
        // Create a set of field names and keyword variations that are used for counting
        global $wp_query;
        $searchPhrase = $wp_query->query_vars['s'];

        // Default keyword counter fields and values ordered from highest to lowest weight
        $keyword_fields = [
            // Quotes around the keyword
            'keywordCountQuotes' => '"' . $searchPhrase . '"',
            // Spaces around the keyword
            'keywordCountSpaces' => ' ' . $searchPhrase . ' ',
            // Prepended space
            'keywordCountStartSpace' => ' ' . $searchPhrase,
            // Unmodified keyword
            'keywordCountWithoutSpace' => $searchPhrase,
        ];

        // If there are more than one keyword in the search, also add those for counting separately
        $split_keywords = preg_split( "/[\s,-]+/", $searchPhrase);
        if (count($split_keywords) > 1) {
            for ($i = 0; $i < count($split_keywords); $i++) {
                $keyword_fields["keywordItem" . $i] = esc_sql(strtolower($split_keywords[$i]));
            }
        }

        return $keyword_fields;
    }

    // Helper function equivalent to str_replace except that only the first occurence is replaced
    function str_replace_first($from, $to, $content) {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $content, 1);
    }

    function search_add_keyword_count($clauses) {
        // This adds the keyword count fields the SELECT statement of all frontend search queries
        global $wp_query, $wpdb;

        // Only modify the search query
        if (is_admin() || !$wp_query->is_search() || $wp_query->query_vars['s'] === '') {
            return $clauses;
        }

        $keywordFields = $this->get_search_keyword_fields();

        // Loop over each keyword field variation Add a counter for each keyword variation
        foreach ($keywordFields as $fieldName => $keyword) {
            // Formula to calculate how many times the keyword occurs in the post content
            $keywordEscaped = esc_sql(strtolower($keyword));
            $calcKeywordCount = "
                (
                (Length(" . $wpdb->prefix . "posts.post_content) - (
                    Length(Replace(Lower(" . $wpdb->prefix . "posts.post_content), '" . $keywordEscaped . "', ''))
                )) / Length('" . $keywordEscaped . "')
                )
            ";

            // Add the keyword count field to the query fields
            $clauses['fields'] = $calcKeywordCount . 'as ' . $fieldName . ' , ' . $clauses['fields'];
        }

        return $clauses;
    }

    function search_order_by_keyword_count($orderby_statement, $wp_query) {
        // Only modify the search query
        if (is_admin() || !$wp_query->is_search() || $wp_query->query_vars['s'] === '') {
            return $orderby_statement;
        }

        // Add the keyword counter fields to the beginning of the ORDERBY statement
        $keywords = $this->get_search_keyword_fields();
        foreach (array_reverse($keywords) as $fieldName => $keyword) {
            $orderby_statement = $fieldName . " DESC, \n" . $orderby_statement;
        }

        return $orderby_statement;
    }
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_btpl_search_relevance() {
	$plugin = new Bittemple_Search_Relevance();
	$plugin->init();
}
run_btpl_search_relevance();

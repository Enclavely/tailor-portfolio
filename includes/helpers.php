<?php

/**
 * Helper functions.
 *
 * @package Tailor Portfolio
 * @subpackage Helpers
 * @since 1.0.0
 */

defined( 'ABSPATH' ) or die();

if ( ! function_exists( 'tailor_portfolio_page' ) ) {

	/**
	 * Returns the ID of the Portfolio Page.
	 *
	 * @return string
	 */
	function tailor_portfolio_page() {
		return get_theme_mod( '_tailor_portfolio_page' );
	}
}

if ( ! function_exists( 'is_portfolio' ) ) {

	/**
	 * Returns true when viewing the project post type archive.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return bool
	 */
	function is_portfolio() {
		return ( is_post_type_archive( 'project' ) || is_page( tailor_portfolio_page() ) );
	}
}

if ( ! function_exists( 'is_project' ) ) {

	/**
	 * Returns true if a singular project is being viewed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function is_project() {
		return is_singular( array( 'project' ) );
	}
}

if ( ! function_exists( 'is_project_taxonomy' ) ) {

	/**
	 * Returns true if a project taxonomy is being viewed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy
	 * @param string $term
	 *
	 * @return bool
	 */
	function is_project_taxonomy( $taxonomy = '', $term = '' ) {
		if ( empty( $taxonomy ) ) {
			$taxonomy = get_object_taxonomies( 'project' );
		}
		return is_tax( $taxonomy, $term );
	}
}

if ( ! function_exists( 'get_portfolio_page_id' ) ) {

	/**
	 * Adds a state to let users know which page is being used as the Portfolio page.
	 *
	 * @since 1.0.0
	 *
	 * @param $post_states
	 * @param $post
	 *
	 * @return mixed
	 */
	function plugin_portfolio_page_state( $post_states, $post ) {
		if ( $post->ID == get_theme_mod( '_page_project' ) ) {
			$post_states['portfolio'] = __( 'Projects Page' );
		}
		return $post_states;
	}

	add_filter( 'display_post_states', 'plugin_portfolio_page_state', 10, 2 );
}
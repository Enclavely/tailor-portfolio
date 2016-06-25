<?php

/**
 * Project post type and taxonomy registration functions.
 *
 * @package Tailor Portfolio
 * @subpackage Projects
 * @since 1.0.0
 */

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'Portfolio_Post_Types' ) ) {

	class Portfolio_Post_Types {

		/**
		 * Initializes the custom post types and taxonomies for the Portfolio.
		 *
		 * @since 1.0.0
		 * @static
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'register_project_post_type' ) );
			add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
			add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );

			add_action( 'admin_init', array( __CLASS__, 'add_permalink_settings' ) );
			add_action( 'admin_init', array( __CLASS__, 'save_permalink_settings' ) );
			add_action( 'manage_edit-project_columns', array( __CLASS__, 'project_columns' ) );
			add_action( 'manage_project_posts_custom_column', array( __CLASS__, 'project_thumbnails' ), 10, 2 );
		}

		/**
		 * Registers the project post type.
		 *
		 * @since 1.0.0
		 * @static
		 */
		static function register_project_post_type() {

			if ( post_type_exists( 'project' ) ) {
				return;
			}

			$permalinks = get_option( '_tailor_portfolio_permalinks' );
			$project_permalink = empty( $permalinks['project_base'] ) ? _x( 'project', 'slug', 'tailor-portfolio' ) : $permalinks['project_base'];

			if ( $project_permalink ) {
				$rewrite_args = array(
					'slug' 					=> 	untrailingslashit( $project_permalink ),
					'with_front' 			=> 	false,
					'feeds' 				=> 	false
				);
			}
			else {
				$rewrite_args = false;
			}

			$portfolio_page_id = tailor_portfolio_page();

			register_post_type( 'project',

				apply_filters( 'tailor_project_parameters',
					array(
						'labels'              => array(
							'name'                      =>  _x( 'Projects', 'project general name' ),
							'singular_name'             =>  _x( 'Project', 'project singular name' ),
							'all_items'                 =>  __( 'All Projects', 'tailor-portfolio' ),
							'add_new'                   =>  _x( 'Add New', 'project', 'tailor-portfolio' ),
							'add_new_item'              =>  __( 'Add New Project', 'tailor-portfolio' ),
							'edit_item'                 =>  __( 'Edit Project', 'tailor-portfolio' ),
							'new_item'                  =>  __( 'Add New Project', 'tailor-portfolio' ),
							'view_item'                 =>  __( 'View Project', 'tailor-portfolio' ),
							'search_items'              =>  __( 'Search Projects', 'tailor-portfolio' ),
							'not_found'                 =>  __( 'No projects found', 'tailor-portfolio' ),
							'not_found_in_trash'        =>  __( 'No projects found in trash', 'tailor-portfolio' ),
						),
						'description'               =>  __( 'This is where you can add new projects to your portfolio.', 'tailor-portfolio' ),
						'public'                    =>  true,
						'show_ui'                   =>  true,
						'map_meta_cap'              =>  true,
						'publicly_queryable'        =>  true,
						'exclude_from_search'       =>  false,
						'hierarchical'              =>  false,
						'rewrite'                   =>  $rewrite_args,
						'query_var'                 =>  true,
						'supports'                  =>  array(
							'title',
							'editor',
							'excerpt',
							'thumbnail',
							'comments',
							'custom-fields',
							'page-attributes',
							'publicize',
							'wpcom-markdown'
						),
						'has_archive'               =>  get_post( $portfolio_page_id ) ? get_page_uri( $portfolio_page_id ) : 'portfolio',
						'show_in_nav_menus'         =>  true
					)
				)
			);
		}

		/**
		 * Registers the Portfolio taxonomies.
		 *
		 * @since 1.0.0
		 */
		static function register_taxonomies() {

			$permalinks = get_option( '_tailor_portfolio_permalinks' );

			register_taxonomy( 'portfolio',
				'project',
				apply_filters( 'tailor_portfolio_parameters', array(
					'labels'                    =>  self::taxonomy_labels( 'Portfolio' ),
					'public'                    =>  true,
					'hierarchical' 				=> 	true,
					'show_ui' 					=> 	true,
					'show_in_nav_menus'         =>  true,
					'query_var' 				=> 	true,
					'capabilities'				=> 	array(),
					'rewrite' 					=> 	array(
						'slug'         				=> 	empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'tailor-portfolio' ) : $permalinks['portfolio_base'],
						'with_front'   				=> 	false,
						'hierarchical'              =>  true,
					),
					'show_tagcloud'             =>  true,
					'show_admin_column'         =>  true,
				) )
			);

			register_taxonomy( 'skill',
				'project',
				apply_filters( 'tailor_skill_parameters', array(
						'labels'                    =>  self::taxonomy_labels( 'Skill' ),
						'public'                    =>  true,
						'hierarchical' 				=> 	false,
						'show_ui' 					=> 	true,
						'query_var' 				=> 	true,
						'capabilities'				=> 	array(),
						'rewrite' 					=> 	array(
							'slug'         				=> 	empty( $permalinks['skill_base'] ) ? _x( 'skill', 'slug', 'tailor-portfolio' ) : $permalinks['skill_base'],
							'with_front'   				=> 	false,
						)
					)
				)
			);
		}

		/**
		 * Returns the set of labels for a given taxonomy.
		 *
		 * @since 1.0.0
		 * @static
		 *
		 * @param $taxonomy_name
		 * @return mixed|void
		 */
		static function taxonomy_labels( $taxonomy_name ) {

			$pluralized_name = self::pluralize_string( $taxonomy_name );

			return apply_filters( 'tailor_taxonomy_labels', array(
				'name' 							=> 	$pluralized_name,
				'singular_name' 				=> 	$taxonomy_name,
				'search_items' 					=>	sprintf( __( 'Search %s', 'tailor-portfolio' ), $pluralized_name ),
				'all_items' 					=> 	sprintf( __( 'All %s', 'tailor-portfolio' ), $pluralized_name ),
				'parent_item' 					=> 	sprintf( __( 'Parent %s', 'tailor-portfolio' ), $taxonomy_name ),
				'parent_item_colon' 			=> 	sprintf( __( 'Parent %s:', 'tailor-portfolio' ), $taxonomy_name ),
				'edit_item' 					=> 	sprintf( __( 'Edit %s', 'tailor-portfolio' ), $taxonomy_name ),
				'update_item' 					=> 	sprintf( __( 'Update %s', 'tailor-portfolio' ), $taxonomy_name ),
				'add_new_item' 					=> 	sprintf( __( 'Add New %s', 'tailor-portfolio' ), $taxonomy_name ),
				'new_item_name' 				=> 	sprintf( __( 'New %s Name', 'tailor-portfolio' ), $taxonomy_name ),
				'separate_items_with_commas'	=> 	sprintf( __( 'Separate %s with commas', 'tailor-portfolio' ), strtolower( $pluralized_name ) ),
				'choose_from_most_used'			=> 	sprintf( __( 'Chose from the most recently used %s', 'tailor-portfolio' ), strtolower( $pluralized_name ) ),
				'menu_name' 					=> 	$pluralized_name,
			) );
		}

		/**
		 * Returns the pluralized version of a string.
		 *
		 * @since 1.0.0
		 * @static
		 *
		 * @param $string
		 * @return string
		 */
		static function pluralize_string( $string ) {
			$last = $string[ strlen( $string ) - 1 ];
			if ( 'y' == $last ) {
				$cut = substr( $string, 0, -1 );
				$plural = $cut . 'ies';
			}
			else {
				$plural = $string . 's';
			}
			return $plural;
		}

		/**
		 * Allows projects to be searched for using the Jetpack Omnisearch plugin.
		 *
		 * @since 1.0.0
		 * @static
		 */
		static function support_jetpack_omnisearch() {
			if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
				new Jetpack_Omnisearch_Posts( 'project' );
			}
		}

		/**
		 * Updates the column configuration for project views in the administrator backend.
		 *
		 * @since 1.0.0
		 * @static
		 *
		 * @return array
		 */
		static function project_columns() {
			$columns = array(
				'cb' 					=>	'<input type="checkbox"/>',
				'title' 				=>	__( 'Title', 'tailor-portfolio' ),
				'thumbnail'				=> 	__( 'Thumbnail', 'tailor-portfolio' ),
			);
			foreach ( (array) $taxonomies = get_object_taxonomies( 'project', 'objects' ) as $taxonomy ) {
				$columns[ 'taxonomy-' . $taxonomy->name ] = ucfirst( self::pluralize_string( $taxonomy->name ) );
			}
			$columns['date'] = __( 'Date', 'tailor-portfolio' );
			return $columns;
		}

		/**
		 * Displays thumbnails, as necessary, for project views in the administrator backend.
		 *
		 * @since 1.0.0
		 * @static
		 *
		 * @param string $column
		 * @param int $post_id
		 */
		static function project_thumbnails( $column, $post_id ) {
			if ( 'thumbnail' != $column ) {
				return;
			}
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'thumbnail' );
			}
			else {
				echo '&#8212';
			}
		}

		/**
		 * Adds setting fields to the Permalinks setting page related to the portfolio.
		 *
		 * @since 1.0.0
		 * @static
		 */
		static function add_permalink_settings() {
			add_settings_field( 'project_slug', __( 'Project base', 'tailor-portfolio' ), array( 'Portfolio_Post_Types', 'project_slug_field' ), 'permalink', 'optional' );
			add_settings_field( 'portfolio_slug', __( 'Portfolio base', 'tailor-portfolio' ), array( 'Portfolio_Post_Types', 'portfolio_slug_field' ), 'permalink', 'optional' );
			add_settings_field( 'skill_slug', __( 'Skill base', 'tailor-portfolio' ), array( 'Portfolio_Post_Types', 'skill_slug_field' ), 'permalink', 'optional' );
		}

		/**
		 * Displays the Project slug option on the Permalinks setting page.
		 *
		 * @since 1.0.0
		 * @static
		 */
		static function project_slug_field() {
			$permalinks = get_option( '_tailor_portfolio_permalinks' );
			$value = isset( $permalinks['project_base'] ) ? esc_attr( $permalinks['project_base'] ) : '';
			printf( '<input name="project_slug" type="text" class="regular-text code" value="%s" placeholder="%s" >', $value,  _x( 'project', 'slug', 'tailor-portfolio' ) );
		}

		/**
		 * Displays the Portfolio slug option on the Permalinks setting page.
		 *
		 * @since 1.0.0
		 * @static
		 */
		static function portfolio_slug_field() {
			$permalinks = get_option( '_tailor_portfolio_permalinks' );
			$value = isset( $permalinks['portfolio_base'] ) ? esc_attr( $permalinks['portfolio_base'] ) : '';
			printf( '<input name="portfolio_slug" type="text" class="regular-text code" value="%s" placeholder="%s" >', $value,  _x( 'portfolio', 'slug', 'tailor-portfolio' ) );
		}

		/**
		 * Displays the Skill slug option on the Permalinks setting page.
		 *
		 * @since 1.0.0
		 * @static
		 */
		static function skill_slug_field() {
			$permalinks = get_option( '_tailor_portfolio_permalinks' );
			$value = isset( $permalinks['skill_base'] ) ? esc_attr( $permalinks['skill_base'] ) : '';
			printf( '<input name="skill_slug" type="text" class="regular-text code" value="%s" placeholder="%s" >', $value,  _x( 'skill', 'slug', 'tailor-portfolio' ) );
		}


		/**
		 * Saves permalink settings related to the portfolio.
		 *
		 * @since 1.0.0
		 * @static
		 */
		static function save_permalink_settings() {
			if ( ! is_admin() || ! isset( $_POST['permalink_structure'] ) ) {
				return;
			}

			$project_slug = sanitize_text_field( $_POST['project_slug'] );
			$portfolio_slug = sanitize_text_field( $_POST['portfolio_slug'] );
			$skill_slug = sanitize_text_field( $_POST['skill_slug'] );

			if ( ! $permalinks = get_option( '_tailor_portfolio_permalinks' ) ) {
				$permalinks = array();
			}

			$permalinks['project_base'] = untrailingslashit( $project_slug );
			$permalinks['portfolio_base'] = untrailingslashit( $portfolio_slug );
			$permalinks['skill_base'] = untrailingslashit( $skill_slug );

			update_option( '_tailor_portfolio_permalinks', $permalinks );
		}
	}
}

Portfolio_Post_Types::init();
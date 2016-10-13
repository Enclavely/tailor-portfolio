<?php

if ( ! function_exists( 'tailor_shortcode_projects' ) ) {

    /**
     * Defines the shortcode rendering function for the Projects element.
     *
     * @since 1.0.0
     *
     * @param array $atts
     * @param string $content
     * @param string $tag
     * @return string
     */
    function tailor_shortcode_projects( $atts, $content = null, $tag ) {

	    $atts = shortcode_atts( array(
		    'id'                =>  '',
		    'class'             =>  '',

		    'style'             =>  'default',
		    'layout'            =>  'list',
		    'items_per_row'     =>  1,
		    'masonry'           =>  false,
		    'lightbox'          =>  false,
		    'meta'              =>  'date,excerpt',
		    'posts_per_page'    =>  6,
		    'pagination'        =>  false,

		    'portfolio'         =>  '',
		    'skill'             =>  '',
		    'order_by'          =>  'date',
		    'order'             =>  'DESC',
		    'offset'            =>  0,

		    'autoplay'          =>  '',
		    'fade'              =>  '',
		    'arrows'            =>  '',
		    'dots'              =>  '',

		    'image_size'        =>  'large',
		    'aspect_ratio'      =>  '',
		    'stretch'           =>  false,
	    ), $atts, $tag );

	    $id = ( '' !== $atts['id'] ) ? 'id="' . esc_attr( $atts['id'] ) . '"' : '';
	    $class = trim( esc_attr( "tailor-element tailor-projects tailor-projects--{$atts['style']} tailor-{$atts['layout']} tailor-{$atts['layout']}--projects {$atts['class']}" ) );

	    if ( true == $atts['lightbox'] ) {
		    $class .= ' is-lightbox-gallery';
	    }

	    $items_per_row = (string) intval( $atts['items_per_row'] );
	    $data = tailor_get_attributes(
		    array(
			    'slides'            =>  $items_per_row,
			    'autoplay'          =>  boolval( $atts['autoplay'] ) ? 'true' : 'false',
			    'arrows'            =>  boolval( $atts['arrows'] ) ? 'true' : 'false',
			    'dots'              =>  boolval( $atts['dots'] ) ? 'true' : 'false',
			    'fade'              =>  boolval( $atts['fade'] && '1' == $items_per_row ) ? 'true' : 'false',
		    ),
		    'data-'
	    );

	    $posts_per_page = intval( $atts['posts_per_page'] );
	    $offset = intval( $atts['offset'] );
	    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	    if ( $paged > 1 ) {
		    $offset = ( ( $paged - 1 ) * $posts_per_page ) + $offset;
	    }

	    $query_args = array(
		    'post_type'         =>  'project',
		    'orderby'           =>  $atts['order_by'],
		    'order'             =>  $atts['order'],
		    'posts_per_page'    =>  $posts_per_page,
		    'offset'            =>  $offset,
		    'paged'             =>  $paged,
		    'tax_query'         =>  array(),
	    );

		if ( $atts['portfolio'] ) {
			$query_args['tax_query'][] = array(
				'taxonomy'      =>  'portfolio',
				'field'         =>  'term_id',
				'terms'         =>  explode( ',', $atts['portfolio'] ),
			);
		}

	    if ( $atts['skill'] ) {
		    $query_args['tax_query']['relation'] = 'AND';
		    $query_args['tax_query'][] = array(
			    'taxonomy'      =>  'skill',
			    'field'         =>  'term_id',
			    'terms'         =>  explode( ',', $atts['skill'] ),
		    );
	    }

	    if ( ! empty( $atts['tags'] ) ) {
		    $query_args['tag__and'] = strpos( $atts['tags'], ',' ) ? explode( ',', $atts['tags'] ) : (array) $atts['tags'];
	    }

	    $q = new WP_Query( $query_args );
	    
	    ob_start();

	    tailor_partial( 'loop', $atts['layout'], array(
		    'q'                 =>  $q,
		    'layout_args'       =>  array(
			    'items_per_row'     =>  $atts['items_per_row'],
			    'masonry'           =>  $atts['masonry'],
			    'pagination'        =>  $atts['pagination'],
		    ),
		    'entry_args'        =>  array(
			    'meta'              =>  explode( ',', $atts['meta'] ),
			    'image_size'        =>  $atts['image_size'],
			    'aspect_ratio'      =>  $atts['aspect_ratio'],
			    'stretch'           =>  $atts['stretch'],
			    'lightbox'          =>  $atts['lightbox'],
		    ),
	    ) );

	    $outer_html = '<div ' . trim( "{$id} class=\"{$class}\" {$data}" ) . '>%s</div>';

	    $inner_html = ob_get_clean();

	    /**
	     * Filter the HTML for the element.
	     *
	     * @since 1.1.1
	     *
	     * @param string $outer_html
	     * @param string $inner_html
	     * @param array $atts
	     */
	    $html = apply_filters( 'tailor_shortcode_projects_html', sprintf( $outer_html, $inner_html ), $outer_html, $inner_html, $atts );

	    return $html;
    }

    add_shortcode( 'tailor_projects', 'tailor_shortcode_projects' );
}
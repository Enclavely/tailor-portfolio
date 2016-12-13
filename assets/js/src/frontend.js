
( function( win, $, Tailor ) {

	'use strict';
	
	Tailor.initPortfolioElements = function() {
		$( '.tailor-projects.is-lightbox-gallery' ).each( function() {
			var $el = $( this );
			var instance = $el.data( 'tailorLightbox' );
			if ( instance ) {
				instance.destroy();
				$el.removeData( 'tailorLightbox' );
			}

			$el.tailorLightbox( {
				delegate : '.entry__thumbnail > a'
			} );
		} );
	};

	$( document ).ready( function() {
		Tailor.initPortfolioElements();
	} );

} ) ( window, window.jQuery, window.Tailor || {} );
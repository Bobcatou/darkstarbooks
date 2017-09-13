;(function ( $, window, document, undefined ) {
	'use strict'

	function initBackstretch() {
		var images = $.parseJSON( BackStretchImg2.src );

		$(".front-page-1").backstretch( images,{
			duration: 5000,
			fade: 750
		});
	}

	$( document ).ready( function () {
		if ( typeof BackStretchImg2 === 'object' ) {
			initBackstretch();
		}

		if( BackStretchImg1.src != '' ) {
			$(".front-page-1").backstretch([BackStretchImg1.src]);
		}
	} );

}( jQuery, window, document ));

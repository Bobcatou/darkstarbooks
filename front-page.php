<?php
/**
 * This file adds the Front Page to the Digital Pro Theme.
 *
 * @author StudioPress
 * @package Digital
 * @subpackage Customizations
 */

add_action( 'genesis_meta', 'digital_front_page_genesis_meta' );
/**
 * Add widget support for homepage. If no widgets active, display the default loop.
 *
 */
function digital_front_page_genesis_meta() {

	if ( is_active_sidebar( 'front-page-1' ) || is_active_sidebar( 'front-page-2' ) || is_active_sidebar( 'front-page-3' ) ) {

		//* Enqueue scripts
		add_action( 'wp_enqueue_scripts', 'digital_enqueue_digital_script' );
		function digital_enqueue_digital_script() {

			wp_register_style( 'digitalIE9', get_stylesheet_directory_uri() . '/style-ie9.css', array(), CHILD_THEME_VERSION );
			wp_style_add_data( 'digitalIE9', 'conditional', 'IE 9' );
			wp_enqueue_style( 'digitalIE9' );

			wp_enqueue_script( 'digital-front-script', get_stylesheet_directory_uri() . '/js/front-page.js', array( 'jquery' ), CHILD_THEME_VERSION );
			wp_enqueue_script( 'localScroll', get_stylesheet_directory_uri() . '/js/jquery.localScroll.min.js', array( 'scrollTo' ), '1.2.8b', true );
			wp_enqueue_script( 'scrollTo', get_stylesheet_directory_uri() . '/js/jquery.scrollTo.min.js', array( 'jquery' ), '1.4.5-beta', true );

			wp_enqueue_style( 'digital-front-styles', get_stylesheet_directory_uri() . '/style-front.css', array(), CHILD_THEME_VERSION );

		}

		//* Add front-page body class
		add_filter( 'body_class', 'digital_body_class' );
		function digital_body_class( $classes ) {

			$classes[] = 'front-page';

			return $classes;

		}

		//* Force full width content layout
		add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

		//* Remove breadcrumbs
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

		//* Add widgets on front page
		add_action( 'genesis_after_header', 'digital_front_page_widgets' );

		$journal = get_option( 'digital_journal_setting', 'true' );

		if ( 'true' === $journal ) {

			//* Add opening markup for blog section
			add_action( 'genesis_before_loop', 'digital_front_page_blog_open' );

			//* Add closing markup for blog section
			add_action( 'genesis_after_loop', 'digital_front_page_blog_close' );

		} else {

			//* Remove the default Genesis loop
			remove_action( 'genesis_loop', 'genesis_do_loop' );

			//* Remove .site-inner
			add_filter( 'genesis_markup_site-inner', '__return_null' );
			add_filter( 'genesis_markup_content-sidebar-wrap_output', '__return_false' );
			add_filter( 'genesis_markup_content', '__return_null' );

		}

	}

	if ( is_active_sidebar( 'front-page-1' ) ) {
		//* Enqueue scripts for backstretch
		add_action( 'wp_enqueue_scripts', 'digital_front_page_enqueue_scripts' );
		function digital_front_page_enqueue_scripts() {

			wp_enqueue_script( 'digital-backstretch', get_stylesheet_directory_uri() . '/js/backstretch.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'digital-backstretch-set', get_stylesheet_directory_uri() .'/js/backstretch-set.js' , array( 'digital-backstretch' ), '1.0.0', true );

			if ( function_exists( 'soliloquy' ) ) { // if Soliloquy is active
				// if a Soliloquy slider having the slug of 'front-page-1' is present, send URLs of its slide images to the js file
				$front_page_1_slider = get_page_by_path( 'front-page-1', OBJECT, 'soliloquy' );
			}

			if ( $front_page_1_slider ) {
				// "Display" Front Page 1 slider - i.e., get the array of URLs of slide images using the soliloquy_output filter used earlier and store it in a variable
				$slide_image_urls = soliloquy( 'front-page-1', 'slug', array(), true  );

				// Pass an array named "BackStretchImg2" to the JS file loaded by "digital-backstretch-set" handle i.e., to backstretch-set.js. We are setting "src" key of this array to the above array variable
				wp_localize_script( 'digital-backstretch-set', 'BackStretchImg2', array( 'src' => $slide_image_urls ) );

				// Pass an array named "BackStretchImg1" to the JS file loaded by "digital-backstretch-set" handle i.e., to backstretch-set.js. We are setting "src" key of this array to be empty
				wp_localize_script( 'digital-backstretch-set', 'BackStretchImg1', array( 'src' => '' ) );
			} else { // else the URL of front-page-1.jpg in images directory
				$image = get_option( 'digital-front-image', sprintf( '%s/images/front-page-1.jpg', get_stylesheet_directory_uri() ) );

				//* Load scripts only if custom backstretch image is being used
				if ( ! empty( $image ) ) {
					wp_localize_script( 'digital-backstretch-set', 'BackStretchImg1', array( 'src' => str_replace( 'http:', '', $image ) ) );
				}
			}

		}
	}

}

//* Add widgets on front page
function digital_front_page_widgets() {

	if ( get_query_var( 'paged' ) >= 2 )
		return;

	echo '<h2 class="screen-reader-text">' . __( 'Main Content', 'digital' ) . '</h2>';

	genesis_widget_area( 'front-page-1', array(
		'before' => '<div id="front-page-1" class="front-page-1"><div class="widget-area fadeup-effect"><div class="wrap">',
		'after'  => '</div></div></div>',
	) );

	genesis_widget_area( 'front-page-2', array(
		'before' => '<div id="front-page-2" class="front-page-2"><div class="wrap"><div class="flexible-widgets widget-area fadeup-effect' . digital_halves_widget_area_class( 'front-page-2' ) . '">',
		'after'  => '</div></div></div>',
	) );

	genesis_widget_area( 'front-page-3', array(
		'before' => '<div id="front-page-3" class="front-page-3"><div class="wrap"><div class="flexible-widgets widget-area fadeup-effect' . digital_widget_area_class( 'front-page-3' ) . '">',
		'after'  => '</div></div></div>',
	) );



//* Front Page 4th Row.

		genesis_widget_area( 'front-page-4-left', array(
			'before' => '<div class="front-page-4"><div class="one-third first">',
			'after' => '</div>',
	) );
			genesis_widget_area( 'front-page-4-middle', array(
			'before' => '<div class="one-third">',
			'after' => '</div>',
	) );
			genesis_widget_area( 'front-page-4-right', array(
			'before' => '<div class="one-third">',
			'after' => '</div></div><div class="clearfix"></div>
',	) );


// Add Instagram widget before footer.

	genesis_widget_area( 'instagram', array(
		'before' => '<div class="instagram"><div class="wrap">',
		'after'  => '</div></div>',
	) );


}

//* Add opening markup for blog section
function digital_front_page_blog_open() {

	$journal_text = get_option( 'digital_journal_text', __( 'Our Journal', 'digital' ) );

	if ( 'posts' == get_option( 'show_on_front' ) ) {

		echo '<div id="journal" class="widget-area fadeup-effect"><div class="wrap">';

		if ( ! empty( $journal_text ) ) {

			echo '<h2 class="widgettitle widget-title center">' . $journal_text . '</h2>';

		}

	}

}

//* Add closing markup for blog section
function digital_front_page_blog_close() {

	if ( 'posts' == get_option( 'show_on_front' ) ) {

		echo '</div></div>';

	}

}

//* Run the Genesis function
genesis();

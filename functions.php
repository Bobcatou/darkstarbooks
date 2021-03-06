<?php
/**
 * Digital Pro.
 *
 * This file adds the functions to the Digital Pro Theme.
 *
 * @package Digital
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/digital/
 */

// Start the engine.
include_once( get_template_directory() . '/lib/init.php' );

// Setup Theme.
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

// Set Localization (do not remove).
add_action( 'after_setup_theme', 'digital_localization_setup' );
function digital_localization_setup(){
	load_child_theme_textdomain( 'digital-pro', get_stylesheet_directory() . '/languages' );
}

// Add the theme's helper functions.
include_once( get_stylesheet_directory() . '/lib/helper-functions.php' );

// Add Image upload and Color select to WordPress Theme Customizer.
require_once( get_stylesheet_directory() . '/lib/customize.php' );

// Include Customizer CSS.
include_once( get_stylesheet_directory() . '/lib/output.php' );

// Add the theme's required WooCommerce functions.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php' );

// Add the custom CSS to the theme's WooCommerce stylesheet.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php' );

// Include notice to install Genesis Connect for WooCommerce.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php' );

// Child theme (do not remove).
define( 'CHILD_THEME_NAME', 'Digital Pro' );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/digital/' );
define( 'CHILD_THEME_VERSION', '1.1.2' );

// Enqueue scripts and styles.
add_action( 'wp_enqueue_scripts', 'digital_scripts_styles' );
function digital_scripts_styles() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Montserrat:400,400i,600|Heebo:500,700', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'ionicons', '//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css', array(), CHILD_THEME_VERSION );

	wp_enqueue_script( 'digital-global-scripts', get_stylesheet_directory_uri() . '/js/global.js', array( 'jquery' ), CHILD_THEME_VERSION, true );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'digital-responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menus' . $suffix . '.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
	wp_localize_script(
		'digital-responsive-menu',
		'genesis_responsive_menu',
		digital_responsive_menu_settings()
	);

}

// Define our responsive menu settings.
function digital_responsive_menu_settings() {

	$settings = array(
		'mainMenu'         => __( 'Menu', 'digital-pro' ),
		'menuIconClass'    => 'ionicons-before ion-ios-drag',
		'subMenu'          => __( 'Submenu', 'digital-pro' ),
		'subMenuIconClass' => 'ionicons-before ion-ios-arrow-down',
		'menuClasses'      => array(
			'others'  => array(
				'.nav-primary',
			),
		),
	);

	return $settings;

}

// Add HTML5 markup structure.
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

// Add accessibility support.
add_theme_support( 'genesis-accessibility', array( '404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'skip-links' ) );

// Add screen reader class to archive description.
add_filter( 'genesis_attr_author-archive-description', 'genesis_attributes_screen_reader_class' );

// Add viewport meta tag for mobile browsers.
add_theme_support( 'genesis-responsive-viewport' );

// Add support for custom header
add_theme_support( 'custom-header', array(
	'width'           => 600,
	'height'          => 140,
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'flex-height'     => true,
) );

// Add support for after entry widget.
add_theme_support( 'genesis-after-entry-widget-area' );

// Rename primary and secondary navigation menus.
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Header Menu', 'digital-pro' ), 'secondary' => __( 'Footer Menu', 'digital-pro' ) ) );

// Remove output of primary navigation right extras
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

// Remove navigation meta box.
add_action( 'genesis_theme_settings_metaboxes', 'digital_remove_genesis_metaboxes' );
function digital_remove_genesis_metaboxes( $_genesis_theme_settings_pagehook ) {
	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );
}

// Remove header right widget area.
unregister_sidebar( 'header-right' );

// Add image sizes.
add_image_size( 'front-page-featured', 1000, 700, TRUE );

// Reposition post image.
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'genesis_do_post_image', 4 );

// Reposition primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Reposition secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 12 );

// Reduce secondary navigation menu to one level depth.
add_filter( 'wp_nav_menu_args', 'digital_secondary_menu_args' );
function digital_secondary_menu_args( $args ) {

	if ( 'secondary' != $args['theme_location'] ) {
		return $args;
	}

	$args['depth'] = 1;

	return $args;

}

// Remove skip link for primary navigation.
add_filter( 'genesis_skip_links_output', 'digital_skip_links_output' );
function digital_skip_links_output( $links ) {

	if ( isset( $links['genesis-nav-primary'] ) ) {
		unset( $links['genesis-nav-primary'] );
	}

	return $links;

}

// Remove seondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Remove site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Reposition entry meta in entry header.
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'genesis_post_info', 8 );

// Customize entry meta in the entry header.
add_filter( 'genesis_post_info', 'digital_entry_meta_header' );
function digital_entry_meta_header( $post_info ) {

	$post_info = '[post_author_posts_link] / [post_date] [post_edit]';

	return $post_info;

}

// Customize the content limit more markup.
add_filter( 'get_the_content_limit', 'digital_content_limit_read_more_markup', 10, 3 );
function digital_content_limit_read_more_markup( $output, $content, $link ) {

	$output = sprintf( '<p>%s &#x02026;</p><p class="more-link-wrap">%s</p>', $content, str_replace( '&#x02026;', '', $link ) );

	return $output;

}

// Customize author box title.
add_filter( 'genesis_author_box_title', 'digital_author_box_title' );
function digital_author_box_title() {
	return '<span itemprop="name">' . get_the_author() . '</span>';
}

// Modify size of the Gravatar in the author box.
add_filter( 'genesis_author_box_gravatar_size', 'digital_author_box_gravatar' );
function digital_author_box_gravatar( $size ) {
	return 160;
}

// Modify size of the Gravatar in the entry comments.
add_filter( 'genesis_comment_list_args', 'digital_comments_gravatar' );
function digital_comments_gravatar( $args ) {

	$args['avatar_size'] = 60;

	return $args;

}

// Remove entry meta in the entry footer on category pages.
add_action( 'genesis_before_entry', 'digital_remove_entry_footer' );
function digital_remove_entry_footer() {

	if ( is_single() ) {
		return;
	}

	remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
	remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
	remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

}

// Setup widget counts.
function digital_count_widgets( $id ) {

	global $sidebars_widgets;

	if ( isset( $sidebars_widgets[ $id ] ) ) {
		return count( $sidebars_widgets[ $id ] );
	}

}

// Flexible widget classes.
function digital_widget_area_class( $id ) {

	$count = digital_count_widgets( $id );

	$class = '';

	if ( $count == 1 ) {
		$class .= ' widget-full';
	} elseif ( $count % 3 == 1 ) {
		$class .= ' widget-thirds';
	} elseif ( $count % 4 == 1 ) {
		$class .= ' widget-fourths';
	} elseif ( $count % 2 == 0 ) {
		$class .= ' widget-halves uneven';
	} else {
		$class .= ' widget-halves even';
	}

	return $class;

}

// Flexible widget classes.
function digital_halves_widget_area_class( $id ) {

	$count = digital_count_widgets( $id );

	$class = '';

	if ( $count == 1 ) {
		$class .= ' widget-full';
	} elseif ( $count % 2 == 0 ) {
		$class .= ' widget-halves';
	} else {
		$class .= ' widget-halves uneven';
	}

	return $class;

}

// Add support for 3-column footer widget.
add_theme_support( 'genesis-footer-widgets', 3 );

// Register widget areas.
genesis_register_sidebar( array(
	'id'          => 'front-page-1',
	'name'        => __( 'Front Page 1', 'digital-pro' ),
	'description' => __( 'This is the 1st section on the front page.', 'digital-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-2',
	'name'        => __( 'Front Page 2', 'digital-pro' ),
	'description' => __( 'This is the 2nd section on the front page.', 'digital-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-3',
	'name'        => __( 'Front Page 3', 'digital-pro' ),
	'description' => __( 'This is the 3rd section on the front page.', 'digital-pro' ),
) );



//* Listen to the Wind Media Modifications

add_filter( 'soliloquy_output', 'sk_soliloquy_images_to_array', 10, 2 );
/**
 * Circumvents the slider output and allows access to raw format.
 *
 * @param string $slider   The slider HTML.
 * @param array $data      Array of slider data.
 * @return array URLs of slide images
 */
function sk_soliloquy_images_to_array( $slider, array $data ) {

	// if this is not the Front Page 1 Soliloquy slider, abort
	if ( 43 !== $data['id'] ) { // replace 529 with the id of Front Page 1 slider
		return $slider;
	}

	$images = array(); // Create an array variable to store URLs of all slider images

	// Loop through slide images one by one
	foreach ( (array) $data['slider'] as $id => $item ) {
		// Skip over images that are pending (ignore if in Preview mode)
		if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {
			continue;
		}

		// Store the URL of slide image in a variable
		$src = wp_get_attachment_image_src( $id, 'full' );

		// Store all the URLs of slide images in an array
		$images[] = $src[0];
	}

	// Return the array of slider images' URLs, encoded into JSON string
	return json_encode( $images );

}



// Register widget areas.
genesis_register_sidebar( array(
	'id'          => 'front-page-4-left',
	'name'        => __( 'Front Page 4-Left', 'digital-pro' ),
	'description' => __( 'This is the 4th/left section on the front page.', 'digital-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-4-middle',
	'name'        => __( 'Front Page 4-Middle', 'digital-pro' ),
	'description' => __( 'This is the 4th section/middle on the front page.', 'digital-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-4-right',
	'name'        => __( 'Front Page 4-Right', 'digital-pro' ),
	'description' => __( 'This is the 4th section/right on the front page.', 'digital-pro' ),
) );


// Register Instagram widget area.
genesis_register_sidebar( array(
	'id'          => 'instagram',
	'name'        => __( 'Instagram', 'theme-name' ),
	'description' => __( 'This is the instagram widget area.', 'theme-name' ),
) );



//* Customize footer credits
add_filter( 'genesis_footer_creds_text', 'sp_footer_creds_text' );
function sp_footer_creds_text() {
     echo '<div class="lwm_credits"><p>';
     echo 'Copyright &copy; ';
     echo date('Y');
     echo ' &middot; <a href="http://darkstarbookstore.com">Dark Star Bookstore</a> &middot; Built and Designed by: <a href="http://www.listentothewindmedia.com" title="Listen to the Wind Media">Listen to the Wind Media</a>';
     echo '</p></div>';
}


//*Remove Woo Product Tabs

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}

//Woocommerce 3.0 support for new Lightbox
//https://createandcode.com/broken-photo-gallery-and-lightbox-after-woocommerce-3-0-upgrade/

add_action( 'after_setup_theme', 'woo3lightbox_setup' );

function woo3lightbox_setup() {
add_theme_support( 'wc-product-gallery-lightbox' );
//add_theme_support( 'wc-product-gallery-zoom' );
add_theme_support( 'wc-product-gallery-slider' );
}

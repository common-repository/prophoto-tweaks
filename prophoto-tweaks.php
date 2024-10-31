<?php 
/*
Plugin Name: ProPhoto Tweaks
Plugin URI: http://www.prophotoblogs.com/support/tweaks-plugin/
Description: A motley collection of rare, fringe, bleeding-edge tweaks and workarounds that certain users of the  <a href="http://www.prophotoblogs.com/">ProPhoto</a> theme might want because of rare server issues, or because they're bat sh*t crazy.
Version: 0.7
Author: ProPhoto Blogs
Author URI: http://www.prophotoblogs.com/
License: GPLv2
*/


class ProPhotoTweaks {
	
	
	protected static $opts;
	protected static $defaults = array(
		'remove_facebook_og_meta' => 'unchecked',
		'show_filenames_in_lightbox_galleries' => 'unchecked',
		'show_from_url_upload_tab' => 'unchecked',
		'max_masthead_imgs' => '',
		'max_widget_imgs' => '',
		'max_lightbox_img_size' => '',
	);
	
	
	public static function onSetupTheme() {

		self::$opts = array_merge( self::$defaults, (array) @json_decode( get_option( 'pp_tweaks' ) ) );
		
		/* admin */
		if ( is_admin() ) {
			add_action( 'admin_menu', 'ProPhotoTweaks::addTweaksMenuItem', 15 );
			
			if ( self::$opts['show_from_url_upload_tab'] == 'checked' ) {
				add_filter( 'pp_hide_upload_from_url_tab', '__return_false' );
			}
			
		/* site-front */
		} else {
			
			if ( self::$opts['remove_facebook_og_meta'] == 'checked' ) {
				add_filter( 'pp_facebook_meta', '__return_false' );
			}
		}
		
		/* everywhere */
		if ( is_numeric( self::$opts['max_masthead_imgs'] ) ) {
			add_filter( 'pp_maxmastheadimages', create_function( '', 'return ' .  self::$opts['max_masthead_imgs'] .';' ) );
		}
		
		if ( is_numeric( self::$opts['max_widget_imgs'] ) ) {
			add_filter( 'pp_maxcustomwidgetimages', create_function( '', 'return ' .  self::$opts['max_widget_imgs'] .';' ) );
		}
		
		if ( is_numeric( self::$opts['max_lightbox_img_size'] ) ) {
			add_filter( 'pp_maxlightboxoverlayimgsize', create_function( '', 'return ' .  self::$opts['max_lightbox_img_size'] .';' ) );
		}
		
		if ( self::$opts['show_filenames_in_lightbox_galleries'] == 'checked' ) {
			add_filter( 'pp_post_img_title', create_function( '$title,$postImg', 'return $postImg->wpObj()->post_title;' ), 10, 2 );
		}
		

	}
	
	
	public static function onClassesLoaded() {
		if ( ppUtil::server() == 'IdeaWebServer' ) {
			self::ideaWebServerFixes();
		}
	}
	
	
	protected static function ideaWebServerFixes() {
		if ( ABSPATH == '//' ) {
			add_filter( 'pp_util_urlfrompath', 'ProPhotoTweaks::fixIdeaWebServerUrlFromPath', 10, 2 );
			add_filter( 'pp_folders_url_from_path_fail', 'ProPhotoTweaks::fixIdeaWebServerFoldersUrlFromPath', 10, 3 );
		}
	}

	
	public static function fixIdeaWebServerUrlFromPath( $url, $path ) {
		return untrailingslashit( ROOTURL ) . $path;
	}
	
	
	public static function fixIdeaWebServerFoldersUrlFromPath( $url, $path, $wpURL ) {
		return $wpURL . $path;
	}
	
	
	public static function addTweaksMenuItem() {
		$tweaksPage = add_submenu_page( 
		    "pp-customize",
		    "ProPhoto &raquo; Tweaks",
		    "Tweaks Plugin",
		    'edit_themes',
		    'pp-tweaks',
		    'ProPhotoTweaks::adminPage'
		);
		add_action( "load-$tweaksPage", 'ProPhotoTweaks::adminPageFiles' );
	}
	
	
	public static function adminPageFiles() {
		wp_enqueue_style( 'pp-tweaks-admin-page', plugin_dir_url( __FILE__ ) .'pp-tweaks-admin-page.css' );
	}
	
	
	public static function adminPage() {
		if ( NrUtil::POST( 'pp_POST_identifier', 'pp_tweaks' ) ) {
			$updatedMsg = self::saveChanges( $_POST );
		}
		include( dirname( __FILE__ ) . '/pp_tweaks_admin_page.php' );
	}
	
	
	protected static function saveChanges( $post ) {
		ppNonce::check( $post['pp_POST_identifier'] );
		$toStore = array();
		foreach ( $post as $key => $val ) {
			if ( array_key_exists( $key, self::$defaults ) ) {
				self::$opts[$key] = $val;
				$toStore[$key]    = $val;
			}
		}
		$checkBoxItems = array(
			'remove_facebook_og_meta',
			'show_filenames_in_lightbox_galleries',
			'show_from_url_upload_tab',
		);
		foreach ( $checkBoxItems as $checkBoxItem ) {
			if ( !isset( $post[$checkBoxItem] ) && self::$opts[$checkBoxItem] == 'checked' ) {
				self::$opts[$checkBoxItem] = 'unchecked';
				$toStore[$checkBoxItem]    = 'unchecked';
			}
		}
		
		if ( $toStore ) {
			ppUtil::updateStoredArray( 'pp_tweaks', $toStore );
			return NrHtml::div( 'Options updated.', 'class=updated pp-admin-msg' );
		}
	}
}

if ( get_option( 'template' ) == 'prophoto4' ) {
	add_action( 'pp_classes_loaded', 'ProPhotoTweaks::onClassesLoaded' );
	add_action( 'setup_theme', 'ProPhotoTweaks::onSetupTheme' );
}




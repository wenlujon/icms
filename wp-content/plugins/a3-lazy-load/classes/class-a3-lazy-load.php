<?php
class A3_Lazy_Load
{
	const version = A3_LAZY_VERSION;
	protected $_placeholder_url;
	protected $_skip_images_classes;
	protected $_skip_videos_classes;
	protected static $_instance;

	function __construct() {
		global $a3_lazy_load_global_settings;

		// Disable for Dashboard
		if ( is_admin() ) {
			return;
		}

		// Disable when not allow from global settings
		if ( $a3_lazy_load_global_settings['a3l_apply_lazyloadxt'] == false ) {
			return;
		}

		if ( $a3_lazy_load_global_settings['a3l_apply_to_images'] == false && $a3_lazy_load_global_settings['a3l_apply_to_videos'] == false ) {
			return;
		}

		// Disable when viewing printable page from WP-Print
		if ( intval( get_query_var( 'print' ) ) == 1 || intval( get_query_var( 'printpage' ) ) == 1 ) {
			return;
		}

		// Disable on Opera Mini
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false ) {
			return;
		}

		if ( true == $a3_lazy_load_global_settings['a3l_load_disable_on_wptouch'] && A3_Lazy_Load::is_wptouch() ) {
			return;
		}

		if ( true == $a3_lazy_load_global_settings['a3l_load_disable_on_mobilepress'] && A3_Lazy_Load::is_mobilepress() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
		add_action( 'wp_print_scripts', array( $this, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( $this, 'localize_printed_scripts' ), 5 );

		add_filter( 'a3_lazy_load_html', array( $this, 'filter_html' ), 10, 2 );

		//$this->_placeholder_url = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		$this->_placeholder_url = A3_LAZY_LOAD_IMAGES_URL . '/lazy_placeholder.gif';

		// Apply for Images
		$skip_images_classes = apply_filters( 'a3_lazy_load_skip_images_classes', $a3_lazy_load_global_settings['a3l_skip_image_with_class'] );
		if ( strlen( trim( $skip_images_classes ) ) ) {
			$this->_skip_images_classes = array_map( 'trim', explode( ',', $skip_images_classes ) );
		}
		if ( is_array( $this->_skip_images_classes ) ) {
			$this->_skip_images_classes = array_merge( array('a3-notlazy'), $this->_skip_images_classes );
		} else {
			$this->_skip_images_classes = array('a3-notlazy');
		}

		if ( $a3_lazy_load_global_settings['a3l_apply_to_images'] == true ) {
			add_filter( 'a3_lazy_load_images', array( $this, 'filter_images' ), 10, 2 );

			if ( $a3_lazy_load_global_settings['a3l_apply_image_to_content'] == true ) {
				add_filter( 'the_content', array( $this, 'filter_content_images' ), 10 );
				add_filter( 'bbp_get_reply_content', array( $this, 'filter_content_images' ), 10 );
			}
			if ( $a3_lazy_load_global_settings['a3l_apply_image_to_textwidget'] == true ) {
				add_filter( 'widget_text', array( $this, 'filter_images' ), 200 );
			}
			if ( $a3_lazy_load_global_settings['a3l_apply_image_to_postthumbnails'] == true ) {
				add_filter( 'post_thumbnail_html', array( $this, 'filter_images' ), 200 );
			}
			if ( $a3_lazy_load_global_settings['a3l_apply_image_to_gravatars'] == true ) {
				add_filter( 'get_avatar', array( $this, 'filter_images' ), 200 );
			}
		}

		// Apply for Videos
		$skip_videos_classes = apply_filters( 'a3_lazy_load_skip_videos_classes', $a3_lazy_load_global_settings['a3l_skip_video_with_class'] );
		if ( strlen( trim( $skip_videos_classes ) ) ) {
			$this->_skip_videos_classes = array_map( 'trim', explode( ',', $skip_videos_classes ) );
		}
		if ( is_array( $this->_skip_videos_classes ) ) {
			$this->_skip_videos_classes = array_merge( array('a3-notlazy'), $this->_skip_videos_classes );
		} else {
			$this->_skip_videos_classes = array('a3-notlazy');
		}

		if ( $a3_lazy_load_global_settings['a3l_apply_to_videos'] == true ) {
			add_filter( 'a3_lazy_load_videos', array( $this, 'filter_videos' ), 10, 2 );

			if ( $a3_lazy_load_global_settings['a3l_apply_video_to_content'] == true ) {
				add_filter( 'the_content', array( $this, 'filter_videos' ), 10 );
			}
			if ( $a3_lazy_load_global_settings['a3l_apply_video_to_textwidget'] == true ) {
				add_filter( 'widget_text', array( $this, 'filter_videos' ), 200 );
			}
		}
	}

	static function _instance() {
		if ( ! isset( self::$_instance ) ) {
			$className = __CLASS__;
			self::$_instance = new $className;
		}
		return self::$_instance;
	}

	static function enqueue_scripts() {

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		global $a3_lazy_load_global_settings;

		$a3l_effect = $a3_lazy_load_global_settings['a3l_effect'];
		$effect = 'fadein';
		if ( $a3l_effect != '' ) {
			$effect = $a3l_effect;
		}
		$effect = apply_filters( 'a3_lazy_load_effect' , $effect );

		do_action('before_a3_lazy_load_xt_style');

		wp_register_style( 'jquery-lazyloadxt-fadein-css', apply_filters( 'a3_lazy_load_effect_css', A3_LAZY_LOAD_CSS_URL.'/jquery.lazyloadxt.fadein.css' ), self::version );
		wp_register_style( 'jquery-lazyloadxt-spinner-css', apply_filters( 'a3_lazy_load_effect_css', A3_LAZY_LOAD_CSS_URL.'/jquery.lazyloadxt.spinner.css' ), self::version );

		wp_enqueue_style( 'jquery-lazyloadxt-'.$effect.'-css' );

		do_action('after_a3_lazy_load_xt_style');

		$in_footer = true;
		$theme_loader_function = $a3_lazy_load_global_settings['a3l_theme_loader'];

		if ( $theme_loader_function == 'wp_head' ) {
			$in_footer = false;
		}

		do_action('before_a3_lazy_load_xt_script');

		wp_deregister_script( 'jquery-lazyloadxt' );
		wp_register_script( 'jquery-lazyloadxt', apply_filters( 'a3_lazy_load_main_script', A3_LAZY_LOAD_JS_URL.'/jquery.lazyloadxt'.$suffix.'.js' ), array( 'jquery' ), self::version, $in_footer );
		wp_register_script( 'jquery-lazyloadxt-srcset', apply_filters( 'a3_lazy_load_main_script', A3_LAZY_LOAD_JS_URL.'/jquery.lazyloadxt.srcset'.$suffix.'.js' ), array( 'jquery', 'jquery-lazyloadxt' ), self::version, $in_footer );
		wp_register_script( 'jquery-lazyloadxt-extend', apply_filters( 'a3_lazy_load_extend_script', A3_LAZY_LOAD_JS_URL.'/jquery.lazyloadxt.extend.js' ), array( 'jquery', 'jquery-lazyloadxt', 'jquery-lazyloadxt-srcset' ), self::version, $in_footer );

		wp_enqueue_script( 'jquery-lazyloadxt-extend' );

		do_action('after_a3_lazy_load_xt_script');
	}

	static function localize_printed_scripts() {
		global $a3_lazy_load_global_settings;

		if ( wp_script_is( 'jquery-lazyloadxt' ) ) {
			wp_localize_script( 'jquery-lazyloadxt', 'a3_lazyload_params', apply_filters( 'a3_lazyload_params', array(
				'apply_images' 	=> $a3_lazy_load_global_settings['a3l_apply_to_images'],
				'apply_videos'	=> $a3_lazy_load_global_settings['a3l_apply_to_videos']
			) ) );
		}

		if ( wp_script_is( 'jquery-lazyloadxt-extend' ) ) {
			wp_localize_script( 'jquery-lazyloadxt-extend', 'a3_lazyload_extend_params', apply_filters( 'a3_lazyload_extend_params', array(
				'edgeY' 	=> (int) $a3_lazy_load_global_settings['a3l_edgeY'],
			) ) );
		}
	}

	static function is_wptouch() {
		if ( function_exists( 'bnc_wptouch_is_mobile' ) && bnc_wptouch_is_mobile() ) {
			return true;
		}

		global $wptouch_pro;

		if ( defined( 'WPTOUCH_VERSION' ) || is_object( $wptouch_pro ) ) {

			if ( $wptouch_pro->showing_mobile_theme ) {
				return true;
			}
		}

		return false;
	}

	static function has_wptouch() {
		if ( function_exists( 'bnc_wptouch_is_mobile' ) || defined( 'WPTOUCH_VERSION' ) ) {
			return true;
		}
		return false;
	}

	static function is_mobilepress() {

		if ( function_exists( 'mopr_get_option' ) && WP_CONTENT_DIR . mopr_get_option( 'mobile_theme_root', 1 ) == get_theme_root() ) {
			return true;
		}

		return false;
	}

	static function has_mobilepress() {
		if ( class_exists( 'Mobilepress_core' ) ) {
			return true;
		}

		return false;
	}

	static function filter_html( $content, $include_noscript = null ) {
		if ( is_admin() ) {
			return $content;
		}

		$run_filter = true;
		$run_filter = apply_filters( 'a3_lazy_load_run_filter', $run_filter );

		if ( ! $run_filter ) {
			return $content;
		}

		global $a3_lazy_load_global_settings;

		$A3_Lazy_Load = A3_Lazy_Load::_instance();

		$content = apply_filters( 'a3_lazy_load_html_before', $content );

		if ( $a3_lazy_load_global_settings['a3l_apply_to_images'] == true ) {
			$content = $A3_Lazy_Load->filter_images( $content, $include_noscript );
		}
		if ( $a3_lazy_load_global_settings['a3l_apply_to_videos'] == true ) {
			$content = $A3_Lazy_Load->filter_videos( $content, $include_noscript );
		}

		$content = apply_filters( 'a3_lazy_load_html_after', $content );

		return $content;
	}

	static function filter_images( $content, $include_noscript = null ) {
		if ( is_admin() ) {
			return $content;
		}

		$run_filter = true;
		$run_filter = apply_filters( 'a3_lazy_load_run_filter', $run_filter );

		if ( ! $run_filter ) {
			return $content;
		}

		global $a3_lazy_load_global_settings;

		$A3_Lazy_Load = A3_Lazy_Load::_instance();

		$content = apply_filters( 'a3_lazy_load_images_before', $content );

		$content = $A3_Lazy_Load->_filter_images( $content, $include_noscript );

		$content = apply_filters( 'a3_lazy_load_images_after', $content );

		return $content;
	}

	static function filter_content_images( $content ) {
		$A3_Lazy_Load = A3_Lazy_Load::_instance();
		add_filter( 'wp_get_attachment_image_attributes', array( $A3_Lazy_Load, 'get_attachment_image_attributes' ), 200 );

		return $A3_Lazy_Load->filter_images( $content );
	}

	static function get_attachment_image_attributes( $attr ) {
		$A3_Lazy_Load = A3_Lazy_Load::_instance();

		$attr['data-src'] = $attr['src'];
		$attr['src'] = $A3_Lazy_Load->_placeholder_url;
		$attr['class'] = 'lazy-hidden '. $attr['class'];
		$attr['data-lazy-type'] = 'image';
		if ( isset( $attr['srcset'] ) ) {
			$attr['data-srcset'] = $attr['srcset'];
			$attr['srcset'] = '';
			unset( $attr['srcset'] );
		}

		return $attr;
	}

	protected function _filter_images( $content, $include_noscript = null ) {

		if ( null === $include_noscript ) {
			global $a3_lazy_load_global_settings;

			$include_noscript = $a3_lazy_load_global_settings['a3l_image_include_noscript'];
		}

		$matches = array();
		preg_match_all( '/<img[\s\r\n]+.*?>/is', $content, $matches );

		$search = array();
		$replace = array();

		if ( is_array( $this->_skip_images_classes ) ) {
			$skip_images_preg_quoted = array_map( 'preg_quote', $this->_skip_images_classes );
			$skip_images_regex = sprintf( '/class=".*(%s).*"/s', implode( '|', $skip_images_preg_quoted ) );
		}

		$i = 0;
		foreach ( $matches[0] as $imgHTML ) {

			// don't to the replacement if a skip class is provided and the image has the class, or if the image is a data-uri
			if ( ! ( is_array( $this->_skip_images_classes ) && preg_match( $skip_images_regex, $imgHTML ) ) && ! preg_match( "/src=['\"]data:image/is", $imgHTML ) && ! preg_match( "/src=.*lazy_placeholder.gif['\"]/s", $imgHTML ) ) {
				$i++;
				// replace the src and add the data-src attribute
				$replaceHTML = '';
				$replaceHTML = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . $this->_placeholder_url . '" data-lazy-type="image" data-src=', $imgHTML );
				$replaceHTML = preg_replace( '/<img(.*?)srcset=/is', '<img$1srcset="" data-srcset=', $replaceHTML );

				// add the lazy class to the img element
				if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
					$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1lazy lazy-hidden $2$1', $replaceHTML );
				} else {
					$replaceHTML = preg_replace( '/<img/is', '<img class="lazy lazy-hidden"', $replaceHTML );
				}

				if ( $include_noscript ) {
					$replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';
				}

				array_push( $search, $imgHTML );
				array_push( $replace, $replaceHTML );
			}
		}

		$search = array_unique( $search );
		$replace = array_unique( $replace );

		$content = str_replace( $search, $replace, $content );


		return $content;
	}

	function get_color( $type = 'background' ) {
		$return = '';
		if ( 'off' != $value ) {

		}

		return $return;
	}
 
	static function filter_videos( $content, $include_noscript = null ) {
		if ( is_admin() ) {
			return $content;
		}

		$run_filter = true;
		$run_filter = apply_filters( 'a3_lazy_load_run_filter', $run_filter );

		if ( ! $run_filter ) {
			return $content;
		}

		global $a3_lazy_load_global_settings;

		$A3_Lazy_Load = A3_Lazy_Load::_instance();

		$content = apply_filters( 'a3_lazy_load_videos_before', $content );

		$content = $A3_Lazy_Load->_filter_videos( $content, $include_noscript );

		$content = apply_filters( 'a3_lazy_load_videos_after', $content );

		return $content;
	}

	protected function _filter_videos( $content, $include_noscript = null ) {

		if ( null === $include_noscript ) {
			global $a3_lazy_load_global_settings;

			$include_noscript = $a3_lazy_load_global_settings['a3l_video_include_noscript'];
		}

		//iFrame
		$matches = array();
		preg_match_all( '#<iframe(.*?)></iframe>#is', $content, $matches );

		$search = array();
		$replace = array();

		if ( is_array( $this->_skip_videos_classes ) ) {
			$skip_images_preg_quoted = array_map( 'preg_quote', $this->_skip_videos_classes );
			$skip_images_regex = sprintf( '/class=".*(%s).*"/s', implode( '|', $skip_images_preg_quoted ) );
		}

		$i = 0;
		foreach ( $matches[0] as $imgHTML ) {
			if ( strpos( $imgHTML, 'gform_ajax_frame' ) ) {
				continue;
			}

			// don't to the replacement if a skip class is provided and the image has the class, or if the image is a data-uri
			if ( ! ( is_array( $this->_skip_videos_classes ) && preg_match( $skip_images_regex, $imgHTML ) ) && ! preg_match( "/ data-src=['\"]/is", $imgHTML ) ) {
				$i++;
				// replace the src and add the data-src attribute
				$replaceHTML = '';
				$replaceHTML = preg_replace( '/iframe(.*?)src=/is', 'iframe$1src="' . $this->_placeholder_url . '" data-lazy-type="iframe" data-src=', $imgHTML );

				// add the lazy class to the img element
				if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
					$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1lazy lazy-hidden $2$1', $replaceHTML );
				} else {
					$replaceHTML = preg_replace( '/<iframe/is', '<iframe class="lazy lazy-hidden"', $replaceHTML );
				}

				if ( $include_noscript ) {
					$replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';
				}

				array_push( $search, $imgHTML );
				array_push( $replace, $replaceHTML );
			}
		}

		$search = array_unique( $search );
		$replace = array_unique( $replace );

		$content = str_replace( $search, $replace, $content );

		//Video
		$matches = array();
		preg_match_all( '/<video(.+?)video>/', $content, $matches );

		$search = array();
		$replace = array();

		if ( is_array( $this->_skip_videos_classes ) ) {
			$skip_images_preg_quoted = array_map( 'preg_quote', $this->_skip_videos_classes );
			$skip_images_regex = sprintf( '/class=".*(%s).*"/s', implode( '|', $skip_images_preg_quoted ) );
		}

		$i = 0;
		foreach ( $matches[0] as $imgHTML ) {

			// don't to the replacement if a skip class is provided and the image has the class, or if the image is a data-uri
			if ( ! ( is_array( $this->_skip_videos_classes ) && preg_match( $skip_images_regex, $imgHTML ) ) && ! preg_match( "/ data-src=['\"]/is", $imgHTML ) ) {
				$i++;
				// replace the src and add the data-src attribute


				$replaceHTML = '';
				$replaceHTML = preg_replace( '/video(.*?)src=/is', 'video$1 data-lazy-type="video" data-src=', $imgHTML );
				$replaceHTML = preg_replace( '/video(.*?)poster=/is', 'video$1poster="' . $this->_placeholder_url . '" data-lazy-type="video" data-poster=', $replaceHTML );

				// add the lazy class to the img element
				if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
					$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1lazy lazy-hidden $2$1', $replaceHTML );
				} else {
					$replaceHTML = preg_replace( '/<video/is', '<video class="lazy lazy-hidden"', $replaceHTML );
				}

				if ( $include_noscript ) {
					$replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';
				}

				array_push( $search, $imgHTML );
				array_push( $replace, $replaceHTML );
			}
		}

		$search = array_unique( $search );
		$replace = array_unique( $replace );

		$content = str_replace( $search, $replace, $content );

		//return $content;

		//Embed
		$matches = array();
		preg_match_all( '/<embed\s+.*?>/', $content, $matches );

		$search = array();
		$replace = array();

		if ( is_array( $this->_skip_videos_classes ) ) {
			$skip_images_preg_quoted = array_map( 'preg_quote', $this->_skip_videos_classes );
			$skip_images_regex = sprintf( '/class=".*(%s).*"/s', implode( '|', $skip_images_preg_quoted ) );
		}

		$i = 0;
		foreach ( $matches[0] as $imgHTML ) {

			// don't to the replacement if a skip class is provided and the image has the class, or if the image is a data-uri
			if ( ! ( is_array( $this->_skip_videos_classes ) && preg_match( $skip_images_regex, $imgHTML ) ) && ! preg_match( "/ data-src=['\"]/is", $imgHTML ) ) {
				$i++;
				// replace the src and add the data-src attribute
				$replaceHTML = '';
				//$replaceHTML = str_replace("src", 'data-src', $imgHTML);
				$replaceHTML = preg_replace( '/embed(.*?)src=/is', 'embed$1 data-lazy-type="video" data-src=', $imgHTML );
				// add the lazy class to the img element
				if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
					$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1lazy lazy-hidden $2$1', $replaceHTML );
				} else {
					$replaceHTML = preg_replace( '/<embed/is', '<embed class="lazy lazy-hidden"', $replaceHTML );
				}

				if ( $include_noscript ) {
					$replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';
				}

				array_push( $search, $imgHTML );
				array_push( $replace, $replaceHTML );
			}
		}

		$search = array_unique( $search );
		$replace = array_unique( $replace );

		$content = str_replace( $search, $replace, $content );


		return $content;
	}
}

add_action( 'wp', create_function('', 'if ( ! is_feed() ) { A3_Lazy_Load::_instance(); }'), 10, 0 );
?>

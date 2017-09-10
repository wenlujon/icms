<?php
define('ICMS_VERSION', 'ICMS v1.3');

add_filter( 'wp_mail_from', icms_mail_from, 10 );
function icms_mail_from( $from_email )
{
	$from_email = "do-not-reply@";
	$from_email .= DOMAIN_CURRENT_SITE;

	return $from_email;
}



add_filter( 'wp_mail_from_name', icms_mail_from_name, 10 );
function icms_mail_from_name ( $from_name )
{
	$from_name = get_bloginfo( 'name' );
	$from_name .= "（勿回复）";

	return $from_name;
}


function sanitize_display_name( $username, $strict = 'true' )
{
    if( $strict ) {
        // strip white spaces
        $username = preg_replace('/\s[\s]+/', '', $username);
        $username = sanitize_user(stripslashes($username), false);
        $username = preg_replace( '/[^\x{4e00}-\x{9fa5}a-z0-9_.\-]/u', '', $username );
	return $username;
    }
    else {
    	return $username;
    }
}



add_action( 'after_setup_theme', 'default_attachment_display_settings' );   
function default_attachment_display_settings()
{
	update_option( 'image_default_size', 'full' );   
}  


/*
 * adjust the width to 700 when inserting image
 */
add_filter( 'image_send_to_editor', 'tweak_image_insertion', 10 );

function tweak_image_insertion( $html ) {
	$html = preg_replace( '/width="\d*"\s/', 'width="700" ', $html );
	return $html;
}



function remove_menus_in_toolbar_common()
{
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('search');

	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('about');
	$wp_admin_bar->remove_menu('wporg');
	$wp_admin_bar->remove_menu('documentation');
	$wp_admin_bar->remove_menu('support-forums');
	$wp_admin_bar->remove_menu('feedback');


	$bbs_home_url = get_bloginfo( 'url' );
	if (strpos($bbs_home_url, 'bbs') != false) {
		$wp_admin_bar->remove_menu('new-content');
		$wp_admin_bar->remove_menu('comments');
		$wp_admin_bar->remove_menu('edit');
	}
	else {
		$wp_admin_bar->remove_menu('new-mycred_rank');
	}

}


function remove_menus_in_toolbar_superadmin()
{
	global $wp_admin_bar;

	//$wp_admin_bar->remove_menu('my-sites');		// My Sites
	$wp_admin_bar->remove_menu('network-admin-t'); 		// My Sites->Network Admin->Themes
	$wp_admin_bar->remove_menu('network-admin-p'); 		// My Sites->Network Admin->Plugins
	$wp_admin_bar->remove_menu('network-admin-s'); 		// My Sites->Network Admin->sites

	$wp_admin_bar->remove_menu('edit-site'); 		// Edit Site in dashboard mode



	$wp_admin_bar->remove_menu('dashboard');		// dashboard->dashboard
	$wp_admin_bar->remove_menu('themes');			// dashboard->Themes
	$wp_admin_bar->remove_menu('widgets');                  // dashboard->widgets
	$wp_admin_bar->remove_menu('menus');			// dashboard->menus
	$wp_admin_bar->remove_menu('customize');		// dashboard->customize
	$wp_admin_bar->remove_menu('customize-background');	// dashboard->backgroud
	$wp_admin_bar->remove_menu('customize-header');		// dashboard->header


	$wp_admin_bar->remove_menu('updates');			// update

	//$wp_admin_bar->remove_menu('comments');		// comments

	//$wp_admin_bar->remove_menu('new-content');		// +new
	//$wp_admin_bar->remove_menu('new-post');		// new->Post
	//$wp_admin_bar->remove_menu('new-media');		// new->Media
	//$wp_admin_bar->remove_menu('new-page');		// new->Page
	//$wp_admin_bar->remove_menu('new-user');		// new->User

	$wp_admin_bar->remove_menu('new-mycred_rank');

	$wp_admin_bar->remove_menu('tribe-events');
	//$wp_admin_bar->remove_menu('new-content');

	$wp_admin_bar->remove_node('dem_settings');			// Democrary

	$wp_admin_bar->remove_node('w3tc');                             // W3C-Cache
}


function remove_menus_in_toolbar_normal()
{
	global $wp_admin_bar;

	$wp_admin_bar->remove_menu('site-name'); 		// dashboard in admin bar
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('blog-2-d'); 		// mysites->bbs->dashboard
	$wp_admin_bar->remove_menu('tribe-events');
}


// remove search in toolbar/adminbar
function remove_menus_in_toolbar()
{
	remove_menus_in_toolbar_common();

	if (!is_super_admin()) {
		remove_menus_in_toolbar_normal();		// remove specific menus for normal user
		/* 
		 * even superadmin doesn't need them, why normal use? though there're some menus won't be
		 * shown due to permission
		 */
		remove_menus_in_toolbar_superadmin();
		return;
	}
	
	if (!WP_CUSTOM) {
		return;
	}

	remove_menus_in_toolbar_superadmin();
}

add_action( 'wp_before_admin_bar_render', 'remove_menus_in_toolbar' );


function remove_sidebar_menus_necessary()
{
	remove_menu_page( 'themes.php' );                 //Appearance
	remove_menu_page( 'plugins.php' );                //Plugins
	remove_menu_page( 'tools.php' );                  //Tools
	remove_menu_page( 'profile.php' );

	remove_menu_page( 'update-core.php' );                  //network->Updates

	remove_submenu_page( 'index.php', 'my-sites.php' );
	remove_menu_page( 'sites.php' );
	remove_submenu_page( 'sites.php', 'site-new.php' );

	remove_submenu_page('options-general.php', 'options-permalink.php' );
	remove_submenu_page('settings.php', 'setup.php' );
	remove_submenu_page('settings.php', 'tribe-events-calendar' );

	remove_submenu_page('upload.php','wp-smush-bulk' );
	remove_submenu_page('edit.php?post_type=forum', 'gdbbpress_tools');
	remove_menu_page('edit.php?post_type=fancy-gallery');
	remove_menu_page('rating_system_options');
	remove_submenu_page('users.php', 'solvease-roles-capablities');
	remove_submenu_page('options-general.php', 'bp-components'); 
	remove_submenu_page('options-general.php', 'a3-lazy-load'); 
	remove_submenu_page('options-general.php', 'wpusme_settings_page');
	remove_submenu_page('options-general.php', 'wordpress-plugin-fancy-gallery-options');
	remove_submenu_page('options-general.php', 'post-views-counter');
	remove_submenu_page('options-general.php', 'header-footer/options.php');
	remove_submenu_page('options-general.php', 'open-social/open-social.php');


	remove_menu_page('myCRED_Network');
	remove_menu_page('myCRED');

	$bbs_home_url = get_bloginfo( 'url' );
	if (strpos($bbs_home_url, 'shop') != false) {
		remove_submenu_page('woocommerce', 'wc-status');
		remove_submenu_page('woocommerce', 'wc-addons');
	}
		
	remove_menu_page('w3tc_dashboard');
}

function remove_sidebar_menus()
{
	if (!is_super_admin()) {		// remove the sidebar for normal users anyway
		remove_sidebar_menus_necessary();
		return;
	}

	if (!WP_CUSTOM) {
		return;
	}

	remove_sidebar_menus_necessary();	// only remove the sidebar menus for superadmin under condition

}

add_action( 'admin_head', 'remove_sidebar_menus' );


function icms_dashboard_redirect()
{
	/** @global string $pagenow */
	global $pagenow;

	if (!WP_CUSTOM) {
		return;
	}

	if ('plugins.php' == $pagenow) {
		wp_redirect( home_url() );
		exit;
	}

	if ('themes.php' == $pagenow) {
		wp_redirect( home_url() );
		exit;
	}

	if ('site-new.php' == $pagenow) {
		wp_redirect( home_url() );
		exit;
	}

	if ('update-core.php' == $pagenow) {
		wp_redirect( home_url() );
		exit;
	}

}


add_action( 'admin_init', 'icms_dashboard_redirect' );


function add_tos_to_buddypress()
{
	echo '<div>';
		echo '<span class="toc">';
			echo '注册账户意味着您同意' ;
			echo bloginfo( 'name' );
			echo '的';
			echo '<a href="';	
			echo bloginfo( 'url' );
			echo '/tos">使用条款</a>以及';
			echo '<a href="';
			echo bloginfo( 'url' );
			echo '/privacy">隐私策略</a>。';
		echo '</span>';
	echo '</div>';
	
}
add_action ( 'bp_before_registration_submit_buttons', 'add_tos_to_buddypress', 99);


/*
 * add the customized left footer in dashboard
 */

add_filter( 'admin_footer_text', 'add_admin_footer', 10 );

function add_admin_footer( $html ) {
	return '赛道时光制作';
}

/*
 * add the customized right footer in dashboard
 */

add_filter( 'update_footer', 'add_update_footer', 11 );

function add_update_footer( $html ) {
	return ICMS_VERSION;
}

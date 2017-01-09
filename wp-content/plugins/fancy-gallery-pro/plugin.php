<?php
/*
Plugin Name: Fancy Gallery Pro
Plugin URI: http://dennishoppe.de/wordpress-plugins/fancy-gallery
Description: Fancy Gallery enables you to create and manage galleries and converts your galleries in post and pages to valid HTML5 blocks and associates linked images with a nice and responsive lightbox.
Version: 1.2.20
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

# Load core classes
Include DirName(__FILE__) . '/class.ajax-requests.php';
Include DirName(__FILE__) . '/class.core.php';
Include DirName(__FILE__) . '/class.gallery-post-type.php';
Include DirName(__FILE__) . '/class.lightbox.php';
Include DirName(__FILE__) . '/class.i18n.php';
Include DirName(__FILE__) . '/class.options.php';
Include DirName(__FILE__) . '/class.updates.php';
Include DirName(__FILE__) . '/class.wpml.php';

# Load widgets
Include DirName(__FILE__) . '/widget.random-images.php';
Include DirName(__FILE__) . '/widget.taxonomies.php';
Include DirName(__FILE__) . '/widget.taxonomy-cloud.php';

# Inititalize Plugin
New WordPress\Plugin\Fancy_Gallery\Core(__FILE__);
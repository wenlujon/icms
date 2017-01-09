<?php
/*
Plugin Name: Fancy Gallery Lite
Plugin URI: http://dennishoppe.de/en/wordpress-plugins/fancy-gallery
Description: Fancy Gallery enables you to create and manage galleries and converts your galleries in post and pages to valid HTML5 blocks and associates linked images with a nice and responsive lightbox.
Version: 1.5.37
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/


$plugin_folder = DirName(__FILE__);

# Load core classes
Include $plugin_folder . '/classes/class.core.php';
Include $plugin_folder . '/classes/class.gallery-post-type.php';
Include $plugin_folder . '/classes/class.lightbox.php';
Include $plugin_folder . '/classes/class.i18n.php';
Include $plugin_folder . '/classes/class.mocking-bird.php';
Include $plugin_folder . '/classes/class.options.php';
Include $plugin_folder . '/classes/class.wpml.php';

# Load widgets
Include $plugin_folder . '/widgets/widget.random-images.php';
Include $plugin_folder . '/widgets/widget.taxonomies.php';
Include $plugin_folder . '/widgets/widget.taxonomy-cloud.php';

# Inititalize Plugin: Will cause a synthax error in PHP < 5.3
New WordPress\Plugin\Fancy_Gallery\Core(__FILE__);
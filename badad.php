<?php
/**
* @package badAd
*/

/*
Plugin Name: badAd
Plugin URI: https://github.com/badAd/wordpress
Description: The official badAd.one plugin for WordPress: With a monetizing partner account, use this plugin to easily monetize your WordPress site with text ads and share your own signup referral link. If you need help getting an account, you can <a href="https://badad.one/444/site.html">signup here</a>.
Version: 1.0.0
Author: badAd
Author URI: https://badad.one
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: badad
*/

/*
badAd is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version (GPLv3 or later).

badAd is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with badAd. If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.
*/

// Classic security
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies or bot humans!' );
}

if ( ! class_exists( 'Bad_Ad' ) ) :

class Bad_Ad {

	public $plugin;
	function __construct() {
		$this->plugin = plugin_basename( __FILE__ );
	}

	function badad_settings_init() {
		// Status
		add_option('badad_testlive', 'test');
		register_setting( 'status', 'badad_testlive' );

		// Dev Keys
		add_option('badad_live_pub', null);
		register_setting( 'devkeys', 'badad_live_pub' );
		add_option('badad_live_sec', null);
		register_setting( 'devkeys', 'badad_live_sec' );
		add_option('badad_test_pub', null);
		register_setting( 'devkeys', 'badad_test_pub' );
		add_option('badad_test_sec', null);
		register_setting( 'devkeys', 'badad_test_sec' );

		// Connection
		add_option('badad_call_key', null);
		register_setting( 'connection', 'badad_call_key' );
		add_option('badad_siteslug', null);
		register_setting( 'connection', 'badad_siteslug' );

		// Status
		add_option('badad_access', 'admin');
		register_setting( 'access', 'badad_access' );

	}

	function register() {
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_filter( "plugin_action_links_$this->plugin", array( $this, 'settings_link' ) );
		add_action('admin_init', array( $this, 'badad_settings_init' ) );
	}

	public function settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=badad-settings">Settings</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	public function settings_index() {
		require_once plugin_dir_path( __FILE__ ) . 'settings.php';

	}

	public function add_settings_page() {
		add_options_page( 'badAd', 'badAd', 'edit_posts', 'badad-settings', array( $this, 'settings_index' ), 110 );
	}

	function enqueue() {
		// enqueue all our scripts
		//wp_enqueue_style( 'mypluginstyle', plugins_url( '/assets/badad_style.css', __FILE__ ) );
		//wp_enqueue_script( 'mypluginscript', plugins_url( '/assets/badad_script.js', __FILE__ ) );
	}

	function activate() {
		require_once plugin_dir_path( __FILE__ ) . 'inc/badad-activate.php';
		badAdActivate::activate();
	}

	function deactivate() {
		require_once plugin_dir_path( __FILE__ ) . 'inc/badad-deactivate.php';
		badAdDeactivate::deactivate();
	}

	function uninstall() {
		badAdUninstall::uninstall();
	}

}

// register
if ( class_exists( 'Bad_Ad' )) {
  $badAd = new Bad_Ad();
	$badAd->register();

}

// activation
register_activation_hook( __FILE__, array( $badAd, 'activate' ) ); // Can't use the badAdActivate class because, not being activated, it doesn't exist yet, so we must use the $badAd variable

// deactivation
register_deactivation_hook( __FILE__, array( 'badAdDeactivate', 'deactivate' ) );

// uninstall
register_uninstall_hook( __FILE__, array( 'badAdUninstall', 'uninstall' ) );

// functions
require_once plugin_dir_path( __FILE__ ) . 'functions.php';

endif;

<?php

/**
* Trigger this file on uninstall
*
* @package badAd
*/

// Check WP is triggering this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  die;
}

// Clear Database tables
global $wpdb;
// Dev table
$badAd_name=$wpdb->prefix ."badad_dev";
$wpdb->query( "DROP TABLE  $badAd_name" );
// API key table
$badAd_name=$wpdb->prefix ."badad_api";
$wpdb->query( "DROP TABLE  $badAd_name" );

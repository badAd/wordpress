<?php
/**
* Database tables
*
* @package badAd
*/

function badAd_db_create() {
  global $wpdb;

  // Table defaults
  $charset_collate = $wpdb->get_charset_collate();

  // Dev table
  $badAd_dev_table = $wpdb->prefix ."badad_dev";
  //Add the database
  $wpdb->query("CREATE TABLE IF NOT EXISTS $badAd_dev_table(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nickname` VARCHAR(255) DEFAULT NULL,
    `plugin` ENUM('set', 'notset') NOT NULL,
    `status` ENUM('test', 'live') NOT NULL,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `test_pub_key` VARCHAR(255) DEFAULT NULL,
    `test_sec_key` VARCHAR(255) DEFAULT NULL,
    `live_pub_key` VARCHAR(255) DEFAULT NULL,
    `live_sec_key` VARCHAR(255) DEFAULT NULL,
    `dev_perm_lev` ENUM('administrator', 'editor') NOT NULL,
    `app_perm_lev` ENUM('administrator', 'editor') NOT NULL,
    PRIMARY KEY (`id`)
  ) $charset_collate");
  // First notset entry, otherwise write callback.php if keys exist
  $badAd_plugin_count = $wpdb->get_results("SELECT COUNT(*) FROM $badAd_dev_table ORDER BY id DESC LIMIT 1");
  $rowsf = $wpdb->num_rows;
  if ( $rowsf == 0 ) {
    $wpdb->query("INSERT INTO $badAd_dev_table (plugin) VALUES ('notset')");
  }

  // API table
  $badAd_app_table = $wpdb->prefix ."badad_app";
  // Add the database
  $wpdb->query("CREATE TABLE IF NOT EXISTS $badAd_app_table(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `app` ENUM('set', 'notset') NOT NULL,
    `status` ENUM('pending', 'connected') NOT NULL,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `call_key` VARCHAR(255) DEFAULT NULL,
    `resite_slug` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) $charset_collate");
  // First notset entry
  $badAd_app_state = $wpdb->get_results("SELECT COUNT(*) FROM $badAd_app_table ORDER BY id DESC LIMIT 1");
  $rowsa = $wpdb->num_rows;
  if ($rowsa == 0) {
    $wpdb->query("INSERT INTO $badAd_app_table (app) VALUES ('notset')");
  }
}

function badAd_db_drop() {
  // Check WP is triggering this
  if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
  }

  // Clear Database tables
  global $wpdb;
  // Dev table
  $badAd_dev_table = $wpdb->prefix ."badad_dev";
  $wpdb->query( "DROP TABLE  $badAd_dev_table" );
  // API key table
  $badAd_app_table = $wpdb->prefix ."badad_app";
  $wpdb->query( "DROP TABLE  $badAd_app_table" );
}

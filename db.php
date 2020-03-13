<?php
/* Database tables */

function badAd_create() {

  global $wpdb;

  // Dev table
  $badAd_name=$wpdb->prefix ."badad_dev";
  $wpdb->query("CREATE TABLE $badAd_name(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nickname` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('test', 'live') NOT NULL,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_newkeys` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `test_pub_key` VARCHAR(255) DEFAULT NULL,
    `test_sec_key` VARCHAR(255) DEFAULT NULL,
    `live_pub_key` VARCHAR(255) DEFAULT NULL,
    `live_sec_key` VARCHAR(255) DEFAULT NULL,
    `date_newkeys` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  )");

  // API table
  $badAd_name=$wpdb->prefix ."badad_api";
  $wpdb->query("CREATE TABLE $badAd_name(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` ENUM('pending', 'connected') NOT NULL,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `key` VARCHAR(255) DEFAULT NULL,
    `before_content` BOOLEAN NOT NULL DEFAULT true,
    `before_content_picred` BOOLEAN NOT NULL DEFAULT true,
    `after_content` BOOLEAN NOT NULL DEFAULT true,
    `after_content_picred` BOOLEAN NOT NULL DEFAULT true,
    PRIMARY KEY (`id`)
  )");


}

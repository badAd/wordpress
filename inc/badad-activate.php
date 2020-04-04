<?php
/**
* @package badAd
*/

class badAdActivate {
		public static function activate() {
			// Make sure we create any files if settings were in the database
			include_once (plugin_dir_path( __FILE__ ).'files.php');
			flush_rewrite_rules();
		}
}

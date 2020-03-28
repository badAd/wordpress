<?php
/**
* @package badAd
*/

// Keys
function badad_keys() {
  $connectionFile = plugin_dir_path( __FILE__ ) . 'connection.php';
  $devkeyFile = plugin_dir_path( __FILE__ ) . 'devkeys.php';
  global $wp_filesystem;
  if (empty($wp_filesystem)) {
    require_once (ABSPATH . '/wp-admin/includes/file.php');
    WP_Filesystem();
  }

  if ( $wp_filesystem->exists($devkeyFile) ) {
    include $devkeyFile;
    $badad_devset = true;
  } else {
    $my_developer_pub_key = '';
    $my_developer_sec_key = '';
    $badad_devset = false;
  }
  if ( $wp_filesystem->exists($connectionFile) ) {
    include $connectionFile; // Make sure we get our variable one way or another
    $partner_resiteURL = "https://badad.one/$partner_resiteSLUG/site.html";
    //$badad_connection_file = true;
  } else {
    $partner_call_key = '';
    $partner_resiteSLUG = '444';
    $partner_resiteURL = "https://badad.one/$partner_resiteSLUG/site.html";
    //$badad_connection_file = false;
  }

  // We need our variables
  return compact(
    'partner_call_key',
    'partner_resiteSLUG',
    'partner_resiteURL',
    'my_developer_pub_key',
    'my_developer_sec_key',
    'badad_devset'
    //'badad_connection_file'
  );
}
extract(badad_keys());

// Pic Credit-referral
function badad_refer( $atts = array() ) {
  global $partner_resiteURL;

  // Defaults
    extract(shortcode_atts(array(
      'type' => 'refer'
    ), $atts));

    if (isset($type)) {
      if ($type == 'refer') {
        $content = '<hr class="badad_shortcode badad_txt badad_hr_top"><p style="text-align: center;"><a id="baVrtLnk1" title="Claim your ad credit at badAd.one with this referral link..." rel="nofollow" href="' . $partner_resiteURL . '"><b>Claim your ad credit...</b></a></p><hr class="badad_shortcode badad_txt badad_hr_bot">';
      } elseif ($type == 'pic') {
        $content = '<p style="text-align: center;"><a class="badad_shortcode badad_gif" id="baVrtLnk1" title="Unannoying advertising" rel="nofollow" href="' . $partner_resiteURL . '"><img class="aligncenter" id="baVrtImg1" alt="badAad.one" src="' . plugins_url() . '/badad/assets/badadcred.gif" /></a></p>';
      } elseif ($type == 'domain') {
        $content = '<hr class="badad_shortcode badad_txt badad_hr_top"><p style="text-align: center;"><a id="baVrtLnk1" title="Unannoying advertising" rel="nofollow" href="' . $partner_resiteURL . '"><b>badAd.one</b></a></p><hr class="badad_shortcode badad_txt badad_hr_bot">';
      }
    }

  return $content;
}
add_shortcode('badadrefer', 'badad_refer');

// Embedded ads via API
function badad_ads( $atts = array() ) {
  global $my_developer_sec_key;
  global $partner_call_key;

  // Defaults
    extract(shortcode_atts(array(
      'num' => 2,
      'balink' => 'no',
      'valign' => 'no',
      'hit' => 'no'
    ), $atts));

  // Regex tests
    // $num
    if (filter_var($num, FILTER_VALIDATE_INT, array("options"=>array('min_range'=>0, 'max_range'=>20)))) {
      $num = $num;
    } else {
      $num = 2;
    }
    // $balink
    if ((isset($balink)) && ($balink == 'yes')) {
      $balink = true;
    } else {
      $balink = false;
    }
    // $valign
    if ((isset($valign)) && ($valign == 'yes')) { // Human setting is reverse from the api
      $valign = false;
    } else {
      $valign = true;
    }
    // $hit
    if ((isset($hit)) && ($hit == 'yes')) { // Human setting is reverse from the api (true = hide)
      $hit = false;
    } else {
      $hit = true;
    }

  // Build the _POST the WordPress way: wp_remote_post()
  $body = array(
    'num_ads' => $num, // Optional, 1-20, default 1
    'show_badad_link' => $balink, // Optional, default false
    'inline_div' => $valign, // Optional, default false
    'no_hit' => $hit, // Optional, default false; if TRUE this counts the same shares, but not as a "hit" in stats, use in sequential calls to avoid triggering multiple "hits" in Partner stats when making more than one call on a single page

    'dev_key' => $my_developer_sec_key,
    'call_key' => $partner_call_key
  );

  // _POST envelope the WordPress way: wp_remote_post()
  $args = array(
    'body' => $body,
    'timeout' => '5',
    'redirection' => '5',
    'httpversion' => '1.0',
    'blocking' => true,
    'headers' => array(),
    'cookies' => array()
  );

  // Give the _POST a hearty sendoff the WordPress way: wp_remote_post()
  $response = wp_remote_post('https://api.badad.one/render.php', $args);
  if ((isset($response)) && ($response != '')) {
    // Filter this glob we got back through the API
    $clean_response = $response['body'];
    echo "<p></p>$clean_response<p></p>"; // This $response is the HTML payload fetched from our Dev API
  }

}
add_shortcode('badad', 'badad_ads');

// Fetch Partner meta
function badad_meta() {
  global $my_developer_sec_key;
  global $partner_call_key;

  // Build the _POST the WordPress way: wp_remote_post()
  $body = array(
    'dev_key' => $my_developer_sec_key,
    'call_key' => $partner_call_key,
  );

  // _POST envelope the WordPress way: wp_remote_post()
  $args = array(
    'body' => $body,
    'timeout' => '15',
    'redirection' => '15',
    'httpversion' => '1.0',
    'blocking' => true,
    'headers' => array(),
    'cookies' => array()
  );

  // Give the _POST a hearty sendoff the WordPress way: wp_remote_post()
  $response = wp_remote_post('https://api.badad.one/fetchmeta.php', $args);
  if ((!isset($response)) || ($response == '')) {
    echo "<div class=\"connected\"><p>Connection not working! Is this plugin set to the same <b>test/live</b> status as your Dev App in the badAd Developer Center?</p></div>";
  } else {
    // Filter this glob we got back through the API
    $clean_response = $response['body'];
    echo "<div class=\"connected\"><p></p>$clean_response<p></p></div>"; // This $response is the HTML payload fetched from our Dev API
  }

  // We need our variables
  $connection_meta_response = $clean_response;
  return compact(
    'connection_meta_response'
  );
}

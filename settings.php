<?php
/**
* @package badAd
*/
// Keys
$badad_status = get_option('badad_testlive');
$badad_live_pub = get_option('badad_live_pub');
$badad_live_sec = get_option('badad_live_sec');
$badad_test_pub = get_option('badad_test_pub');
$badad_test_sec = get_option('badad_test_sec');
if (($badad_live_pub == '') || ($badad_live_pub == null)
 || ($badad_live_sec == '') || ($badad_live_sec == null)
 || ($badad_test_pub == '') || ($badad_test_pub == null)
 || ($badad_test_sec == '') || ($badad_test_sec == null)) {
   $badad_plugin = 'notset';
 } else {
   $badad_plugin = 'set';
 }
if ($badad_status == 'live') {
  $write_dev_pub_key = $badad_live_pub;
  $write_dev_sec_key = $badad_live_sec;
} elseif ($badad_status == 'test') {
  $write_dev_pub_key = $badad_test_pub;
  $write_dev_sec_key = $badad_test_sec;
}
// Access
$badad_access = get_option('badad_access');
if ( $badad_access == 'admin' ) {
  $badAd_drole = 'administrator';
  $badAd_arole = 'administrator';
} elseif ( $badad_access == 'admineditor' ) {
  $badAd_drole = 'administrator';
  $badAd_arole = 'editor';
} elseif ( $badad_access == 'editor' ) {
  $badAd_drole = 'editor';
  $badAd_arole = 'editor';
}
// Set these per use (not standard style, but poetically brief)
if ($badAd_drole == 'administrator') {$badAd_dlevel = 'activate_plugins';}
elseif ($badAd_drole == 'editor') {$badAd_dlevel = 'edit_others_posts';}
if ($badAd_arole == 'administrator') {$badAd_alevel = 'activate_plugins';}
elseif ($badAd_arole == 'editor') {$badAd_alevel = 'edit_others_posts';}

/* Note to developers and WordPress.org reviewers

- For speed, keys for regular calls to the badAd API should utilize include(), rather than SQL queries
- These four files are created when adding keys:
  - callback.php (created automatically by the badAd settings dashboard [this file, settings.php] after adding Dev Keys, used to talk to our API)
  - devkeys.php  (created automatically by the badAd settings dashboard from settings stored using the WP native settings-database calls)
  - connection.php (created when a user authorizes an API connection, used to store related connection "call" keys, these keys are stored nowhere else, this keeps the app light-weight)
  - disconnect.php (created automatically by the badAd settings dashboard after a user authorizes an API connection, used to delete connection.php when clicking to Disconnect from the badAd settings dashboard, this file remains after connection.php is deleted, but is then useless)
- Only devkeys.php and connection.php serve as our framework, having variables developers need to build on for plugins and themes dependent on this plugin:
- What the framework files look like:
  - devkeys.php:
    ```
    $my_developer_pub_key = 'some_pub_0123456789abcdfghijklmnopqruvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0abcd';
    $my_developer_sec_key = 'some_sec_0123456789abcdfghijklmnopqruvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0abcd';
    ```
  - connection.php:
    ```
    $partner_call_key = 'some_pub_0123456789abcdfghijklmnopqruvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0abcd';
    $partner_resiteSLUG = '0123456789abcdfghijklmnopqruvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdfghijklmnopqruvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdfghijklmnopqruvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdfghijklmnopqruvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789abcdefghij';
    ```

*/

// Write our include files //
// Write callback.php
// Initiate $wp_filesystem
global $wp_filesystem;
if (empty($wp_filesystem)) {
  require_once (ABSPATH . '/wp-admin/includes/file.php');
  WP_Filesystem();
}
// Write connect.php
$callbackFile = plugin_dir_path( __FILE__ ).'callback.php';
$connectionKeyFile = plugin_dir_path( __FILE__ ).'connection.php';
$connectionDelFile = plugin_dir_path( __FILE__ ).'disconnect.php';
$badadSettingsPage = admin_url( 'options-general.php?page=badad-settings' );

if (( ! $wp_filesystem->exists($callbackFile) ) || (strpos ( file_get_contents($callbackFile), $write_dev_pub_key) === false )) {
  $callbackContentsPHP = <<<'EOP'
<?php
if ((isset($_POST['badad_connect_response']))
&& (isset($_POST['partner_app_key']))
&& (isset($_POST['partner_call_key']))
&& (isset($_POST['partner_refcred']))
&& (preg_match ('/[a-zA-Z0-9_]$/i', $_POST['partner_app_key']))
&& (preg_match ('/[a-zA-Z0-9_]$/i', $_POST['partner_call_key']))
&& (preg_match ('/^call_key_(.*)/i', $_POST['partner_call_key']))
&& (preg_match ('/[a-zA-Z0-9]$/i', $_POST['partner_refcred']))) { // _POST all present and mild regex check
$partner_call_key = $_POST['partner_call_key']; // Starts with: "call_key_" Keep this in your database for future API calls with this connected partner, it starts with: "call_key_"
$partner_refcred = $_POST['partner_refcred']; // The "resite.html" URL, acting as BOTH a badAd click for Partner shares AND as a referral link for ad credits uppon purchase of a new customer
$connectionKeyContents = <<<EKK
<?php
\$partner_call_key = '$partner_call_key';
\$partner_resiteSLUG = '$partner_refcred';
EKK;
EOP;
  $connectionKeyFileContents = <<<EOK
file_put_contents('$connectionKeyFile', \$connectionKeyContents);
header("Location: $badadSettingsPage");
exit();
}
?>
EOK;
  $callbackContentsHTML = <<<EOH
<!DOCTYPE html>
<html>
<head>
<meta name="badad.api.dev.key" content="$write_dev_pub_key" />
</head>
<body>
No script kiddies.
</body>
</html>
EOH;

  $callbackContents = $callbackContentsPHP."\n".$connectionKeyFileContents."\n".$callbackContentsHTML;
  $wp_filesystem->put_contents( $callbackFile, $callbackContents, FS_CHMOD_FILE ); // predefined mode settings for WP files
}
// end callback.php

// Double check disconnect.php
if (( $wp_filesystem->exists($connectionKeyFile) ) && ( ( ! $wp_filesystem->exists($connectionDelFile) ) || (strpos ( file_get_contents($connectionDelFile), $badad_test_sec) === false ))) {
  $connectionDelete = <<<CDEL
<?php
if ((\$_SERVER['REQUEST_METHOD'] === 'POST') && (isset(\$_POST['dk'])) && (\$_POST['dk'] == '$badad_test_sec')) {
unlink('$connectionKeyFile');
}
header("Location: $badadSettingsPage");
exit();
?>
CDEL;
$wp_filesystem->put_contents( $connectionDelFile, $connectionDelete, FS_CHMOD_FILE ); // predefined mode settings for WP files
}

// Write devkeys.php
if ( $badad_status == 'live' ) {
  $devKeysContents = <<< EDK
<?php
\$my_developer_pub_key = '$badad_live_pub';
\$my_developer_sec_key = '$badad_live_sec';
EDK;
} elseif ( $badad_status == 'test' ) {
  $devKeysContents = <<< EDK
<?php
\$my_developer_pub_key = '$badad_test_pub';
\$my_developer_sec_key = '$badad_test_sec';
EDK;
}
$devKeysFile = plugin_dir_path( __FILE__ ).'devkeys.php'; // a better way
$wp_filesystem->put_contents( $devKeysFile, $devKeysContents, FS_CHMOD_FILE ); // predefined mode settings for WP files
// end devkeys.php

// Fetch the settings from the files we just made
extract(badad_keys());
 ?>

<div class="wrap">
  <h1>badAd</h1>

<?php
// Check for a writable plugin directory
$path = plugin_dir_path( __FILE__ );
if (!wp_is_writable($path)) {
  echo "<h2>Your 'badad' plugin folder is not writable on the server!</h2>
  <p>If you are using Apache, you might need to run:</p>
  <pre>sudo chown -R www-data:www-data $path</pre>
  <p>We can't do anymore until this gets fixed.</p>";
  exit();
}

// Check keys
if ( ( current_user_can($badAd_dlevel) ) && ( $badad_plugin == 'notset' ) ) {
  // add Dev keys
  $callbackURL = plugin_dir_url('badad').'badad/callback.php'; // a better way
  echo '<h2>Add your badAd Developer API keys to get started!</h2>
  <p>These keys can be found or created in your <a target="_blank" href="https://badad.one">badAd.one</a> Partner Center > Developer Center</p>
  <p><pre>Dev Callback URL: <b>'.$callbackURL.'</b> <i>(for badAd Developer Center: Dev App settings)</i></pre></p>
  <br>
  <form method="post" action="options.php">';
    settings_fields( 'devkeys' );
    echo '<h4>Keys</h4>
    <label for="badad_live_pub">Live Public Key:</label>
    <input name="badad_live_pub" type="text" style="width: 100%" ><br>
    <label for="badad_live_sec">Live Secret Key:</label>
    <input name="badad_live_sec" type="text" style="width: 100%" ><br>
    <label for="badad_test_pub">Test Public Key:</label>
    <input name="badad_test_pub" type="text" style="width: 100%" ><br>
    <label for="test_sec_key">Test Secret Key:</label>
    <input name="badad_test_sec" type="text" style="width: 100%" ><br>
    <br>
    <input type="checkbox" name="double_check_key_update" value="certain" required>
    <label for="double_check_delete"> I am sure I want to update the keys.</label>
    <input class="button button-secondary" type="submit" value="Update all keys as shown">
  </form>
  <br><hr>
  <h2>Need help?</h2>
  <p><a target="_blank" href="https://badad.one/help_videos.php">Learn more</a> or sign up to start monetizing today!</p>
  <p>You must be registered, have purchased one (ridiculously cheap) ad, and confirmed your email to be a <a target="_blank" href="https://badad.one">badAd.one</a> Partner. It could take as little as $1 and 10 minutes to be up and running! <a target="_blank" href="https://badad.one/help_videos.php">Learn more</a>.</p>';
} elseif ( ( current_user_can($badAd_alevel) ) && ( $badad_connection == false ) ) {
  // Forms to connect

  // User app_key
  echo '
  <form id="connect_partner_app_id" class="connect_partner" action="https://badad.one/connect_app.php" method="post" accept-charset="utf-8">
  <p><b>Connect with a Partner App Key</b></p>

  <!-- DEV NEEDS THIS -->
  <input type="hidden" name="dev_key" value="'.$my_developer_sec_key.'" />

  <label for="partner_app_key">Your Partner App Key:</label>
  <br /><br />

  <!-- DEV NEEDS THIS: name="partner_app_key" -->
  <input type="text" name="partner_app_key" id="partner_app_key" size="32" required />

  <input class="button button-primary" type="submit" value="Connect" class="formbutton" />
  <br />
  </form>';

  // Be pretty
  echo "<br /><hr /><br />";

  // User login
  echo '
  <form id="connect_partner_app_id" class="connect_partner" action="https://badad.one/connect_app.php" method="post" accept-charset="utf-8">
  <p><b>Connect by login</b></p>

  <!-- DEV NEEDS THIS -->
  <input type="hidden" name="dev_key" value="'.$my_developer_sec_key.'" />

  <input class="button button-primary" type="submit" value="Login to Connect..." class="formbutton" />
  <br />
  </form>';

  // Be pretty
    echo "<br /><hr /><br />";

  // Backup
  echo '
  <p><b>Reconnect by backup</b></p>
  <p>Alternatively: if you have a backup of <b>connection.php</b>, upload it to the "badad" plugin folder on the server. (Note, it will only work with the original badAd Dev App you first connected it to.)</p>';

  // Be pretty
  echo "<br /><hr /><br />";

} elseif ( current_user_can('edit_posts') ) {
  // All Contributors
  // Shortcode help
  echo "<h2>Shortcodes:</h2>";
  echo "<p><pre><b>[badadrefer]</b> <i>Count-shares-if-clicked referral link, with badAd logo image, no view share or hit count (loads fast)</i></pre></p>";
  echo "<p><pre><b>[badadrefertxt]</b> <i>Count-shares-if-clicked referral link, text link only, no view share or hit count (loads fast)</i></pre></p>";
  echo "<br>";
  echo "<p><pre><b>[badad]</b> <i>Retrieve ads from badAd, share count (too many slows your website load time)</i></pre></p>";
  echo "<h3><pre>[badad] options:</pre></h3>";
  echo "<p><pre><b>[badad num=2 balink=no valign=no hit=no]</b> <i>(Defaults, two ads side-by-side)</i></pre></p>";
  echo "<p><pre> <b>num=</b> <i>Number 1-20: how many ads to show (1 share per ad)</i></pre></p>";
  echo "<p><pre> <b>balink=</b> <i>yes/no: Count-shares-if-clicked referral link, text only (share count of 1 ad)</i></pre></p>";
  echo "<p><pre> <b>valign=</b> <i>yes/no: Align ads vertically? (no effect on share count)</i></pre></p>";
  echo "<p><pre> <b>hit=</b> <i>yes/no: Count as \"hit\" in Project Stats? (no effect on share count)</i><br><i> Tip: Set exactly ONE [badad] shortcode to 'hit=true' per page for accurate Stats</i></pre></p>";
  echo "<hr>";

}

// Plugin Settings
if ( current_user_can($badAd_alevel) ) {
  echo "<h2>Plugin Settings:</h2>";

  // App Connection
  if ( current_user_can($badAd_alevel) ) {
    // App Project
    if ( $badad_connection == true ) {
      echo "<p><i><b>Connected to App Project:</b><br>";
      extract(badad_meta()); // use extract because we will use the response variable later
      echo "</i></p><hr>" ;
    } elseif ( $badad_connection == false ) {
      echo "<p><b>Use the form above to connect.</b><br>";
    }
  }

  // Dev keys & callback
  if ( current_user_can($badAd_dlevel) ) {
    // Important info
    // Create the badAd Dev Callback URL
    //$callbackURL = get_home_url().'/wp-content/plugins/badad/callback.php'; // Works, but only maybe
    $callbackURL = plugin_dir_url('badad').'badad/callback.php'; // a better way
    echo "<p><pre>WP Plugin Status: <b>$badad_status</b></pre></p>";
    echo "<p><pre>Dev Callback URL: <b>$callbackURL</b> <i>(for badAd Developer Center: Dev App settings)</i></pre></p>";
    echo "<p><pre>Current Public Key: <b>$my_developer_pub_key</b></pre></p>";
    echo "<hr>" ;
  }
}

// Settings
if ( current_user_can($badAd_alevel) ) {
  echo "<h3>Danger Zone:</h3>";

  // Change Dev keys/status
  if ( current_user_can($badAd_dlevel) ) {
    echo '
    <button class="button button-primary" onclick="showDevKeysStatus()">Dev keys & status <b>&darr;&darr;&darr;</b></button>
    <div id="devKeysStatus" style="display:none">

    <!-- Status radio form -->
    <form method="post" action="options.php">';
      settings_fields( 'status' );
      echo '<h4>Status</h4>
      <input type="radio" name="badad_testlive" value="live"';
      checked('live', $badad_status, true);
      echo '> Live<br>

      <input type="radio" name="badad_testlive" value="test"';
      checked('test', $badad_status, true);
      echo '> Test<br>
      <br>

      <input class="button button-secondary" type="submit" value="Save status">
    </form>
    <br><br>

    <!-- Update keys form -->
    <form method="post" action="options.php">';
      settings_fields( 'devkeys' );
      echo '<h4>Keys</h4>
      <label for="badad_live_pub">Live Public Key:</label>
      <input name="badad_live_pub" type="text" style="width: 100%" value="';
      echo $badad_live_pub;
      echo '" ><br>
      <label for="badad_live_sec">Live Secret Key:</label>
      <input name="badad_live_sec" type="text" style="width: 100%" value="';
      echo $badad_live_sec;
      echo '" ><br>
      <label for="badad_test_pub">Test Public Key:</label>
      <input name="badad_test_pub" type="text" style="width: 100%" value="';
      echo $badad_test_pub;
      echo '" ><br>
      <label for="test_sec_key">Test Secret Key:</label>
      <input name="badad_test_sec" type="text" style="width: 100%" value="';
      echo $badad_test_sec;
      echo '" ><br>
      <br>
      <input type="checkbox" name="double_check_key_update" value="certain" required>
      <label for="double_check_delete"> I am sure I want to update the keys.</label>
      <input class="button button-secondary" type="submit" value="Update all keys as shown">
    </form>
    <p>You can update these keys from the same Dev App and it will not disconnect your ads.</p>
    <hr>
    </div>
    <script>
    function showDevKeysStatus() {
      var x = document.getElementById("devKeysStatus");
      if (x.style.display === "block") {
        x.style.display = "none";
      } else {
        x.style.display = "block";
      }
    }
    </script>
    <br><br>
    ';
  }

  // Delete App Call keys
  if (( current_user_can($badAd_alevel) ) && ( isset($connection_meta_response) )) {
    $disconnectkURL = plugin_dir_url('badad').'badad/disconnect.php';
    echo '
    <button class="button button-primary" onclick="showAppConnection()">App connection <b>&darr;&darr;&darr;</b></button>
    <div id="appConnection" style="display:none">
    <h4>Delete current App connection?</h4>
    <p><i>Currently connected to badAd App Project:<br>'.$connection_meta_response.'</i></p>
    <form method="post" action="'.$disconnectkURL.'">
    <input type="hidden" name="dk" value="'.$badad_test_sec.'">
    <input type="checkbox" name="double_check_delete" value="certain" required>
    <label for="double_check_delete"> I am sure I want to delete this connection.</label>
    <input class="button button-secondary" type="submit" value="Disconnect and delete forever!">
    </form>
    <p>If you plan to recover this connection, backup <b>connection.php</b> from the "badad" plugin folder on the server before disconnecting.</p>
    <hr>
    </div>
    <script>
    function showAppConnection() {
      var x = document.getElementById("appConnection");
      if (x.style.display === "block") {
        x.style.display = "none";
      } else {
        x.style.display = "block";
      }
    }
    </script>
    <br><br>
    ';
  }
}
// Who can change plugin keys and connection
if ( current_user_can('update_plugins') ) { // Only admins or super admins
  // js button "User level settings..."
  // Radio options: Administrator for all; Administrator for Dev keys, Editor for App connection; Editor for all
  echo '
  <button class="button button-primary" onclick="showPluginAccess()">Plugin access <b>&darr;&darr;&darr;</b></button>
  <div id="pluginAccess" style="display:none">
    <form method="post" action="options.php">';
      settings_fields( 'access' );
      echo '<h4>Who can change Dev keys and App connection?</h4>
      <input type="radio" name="badad_access" value="admin"';
      checked('admin', $badad_access, true);
      echo '> Administrator for all<br>
      <input type="radio" name="badad_access" value="admineditor"';
      checked('admineditor', $badad_access, true);
      echo '> Administrator for Dev keys, Editor for App connection
      <br>
      <input type="radio" name="badad_access" value="editor"';
      checked('editor', $badad_access, true);
      echo '> Editor for all<br>
      <br>
      <br><br>
      <input class="button button-secondary" type="submit" value="Save">
    </form>
    <br><hr>
  </div>
  <script>
  function showPluginAccess() {
    var x = document.getElementById("pluginAccess");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }
  </script>
  ';
}

?>
</div>

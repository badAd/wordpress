<?php
/**
* @package badAd
*/
// Keys & Files
include_once (plugin_dir_path( __FILE__ ).'files.php');

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
  <p>These keys can be found or created in your badAd.one <i>Partner Center > Developer Center</i>. For help or to create an account, see the <a target="_blank" href="https://badad.one/444/site.html">help videos here</a>.</p>
  <p><pre>Dev Callback URL: <b>'.$callbackURL.'</b> <i>(for badAd Developer Center: Dev App settings)</i></pre></p>
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
  <p>You must be registered, have purchased one (ridiculously cheap) ad, and confirmed your email to be a <a target="_blank" href="https://badad.one/444/site.html">badAd.one</a> Dev Partner. It could take as little as $1 and 10 minutes to be up and running! <a target="_blank" href="https://badad.one/444/site.html">Learn more</a>.</p>
  <p><iframe width="640" height="360" scrolling="no" frameborder="0" style="border: none;" src="https://www.bitchute.com/embed/VBTAknEAACKJ/"></iframe></p>';

} elseif ( ( current_user_can($badAd_alevel) ) && ( $badad_connection_file == false ) && ( $badad_connection == 'notset' ) ) {
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
  </form>
  <br><hr>
  <h2>Need help?</h2>
  <p><a target="_blank" href="https://badad.one/help_videos.php">Learn more</a> or sign up to start monetizing today!</p>
  <p>You must be registered, have purchased one (ridiculously cheap) ad, and confirmed your email to be a <a target="_blank" href="https://badad.one/444/site.html">badAd.one</a> Partner. It could take as little as $1 and 10 minutes to be up and running! <a target="_blank" href="https://badad.one/444/site.html">Learn more</a>.</p>
  <p><iframe width="640" height="360" scrolling="no" frameborder="0" style="border: none;" src="https://www.bitchute.com/embed/mZSpkFWnCbxo/"></iframe></p>';

  // Be pretty
    echo "<br /><hr /><br />";

} elseif ( current_user_can('edit_posts') ) {
  // All Contributors
  // Shortcode help
  echo "<h2>Shortcodes:</h2>";
  echo "<h3><pre>[badad]</pre></h3>";
  echo "<p><pre><i>Retrieve ads from badAd, share count</i></pre></p>";
  echo "<p><pre><b>[badad num=2 balink=no valign=no hit=no]</b> <i>(Defaults, two ads side-by-side)</i></pre></p>";
  echo "<p><pre> <b>num=</b> <i>Number 1-20: how many ads to show (1 share per ad)</i></pre></p>";
  echo "<p><pre> <b>balink=</b> <i>yes/no: Count-shares-if-clicked referral link, text only (share count of 1 ad)</i></pre></p>";
  echo "<p><pre> <b>valign=</b> <i>yes/no: Align ads vertically? (no effect on share count)</i></pre></p>";
  echo "<p><pre> <b>hit=</b> <i>yes/no: Count as \"hit\" in Project Stats? (no effect on share count)</i><br><i> Tip: Set exactly ONE [badad] shortcode to 'hit=true' per page for accurate Stats</i></pre></p>";
  echo "<br>";
  echo "<h3><pre>[badadrefer]</pre></h3>";
  echo "<p><pre><i>Count-shares-if-clicked referral link, no view share or hit count (loads fast)</i></pre></p>";
  echo "<p><pre><b>[badadrefer type=refer]</b> <i>Text: <b>Claim your ad credit...</b> (Default)</i></pre></p>";
  echo "<p><pre> <b>type=domain</b> <i>Text: <b>badAd.one</b></i></pre></p>";
  echo "<p><pre> <b>type=pic</b> <i>Shows a small banner-ad that cycles badAd logos and slogans (may change when plugin is updated)</i></pre></p>";
  echo '<br><p><i>Watch the <a target="_blank" href="https://www.bitchute.com/video/BkIMAjWX4jii/">help video on badAd-WordPress shortcodes</a></i></p>';
  echo "<hr>";

}

// Plugin Settings
if ( current_user_can($badAd_alevel) ) {
  echo "<h2>Connection Status:</h2>";

  // App Connection
  if (( $badad_connection == 'set' ) && ( $badad_connection_file == true )) {
    echo "<p><i><b>Connected to App Project:</b></i></p>";
    extract(badad_meta()); // use extract because we will use the response variable later
  } elseif ((( $badad_connection == 'notset' ) && ( $badad_connection_file == true  ))
    ||      (( $badad_connection == 'set'    ) && ( $badad_connection_file == false ))) {
    echo "<p><i>Connection just established. Reload this page to see your app connection status.<br></i></p>";
  } elseif  (( $badad_connection == 'notset' ) && ( $badad_connection_file == false )) {
  echo "<p><b>Use the form above to connect.</b></p>";
  }

  echo "<hr>";

  // Dev keys & callback
  if ( current_user_can($badAd_dlevel) ) {
    // Important info
    echo "<h2>Reference:</h2>";
    $callbackURL = plugin_dir_url('badad').'badad/callback.php'; // a better way
    echo "<p><pre>WP Plugin Status: <b>$badad_status</b></pre></p>";
    echo "<p><pre>Dev Callback URL: <b>$callbackURL</b> <i>(for badAd Developer Center: Dev App settings)</i></pre></p>";
    echo "<p><pre>Current Public Key: <b>$my_developer_pub_key</b></pre></p>";
    echo "<hr>" ;
  }
}

// Settings
if ( current_user_can($badAd_alevel) ) {
  echo "<h3>Danger Zone: Make changes</h3>";

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
    echo '
    <button class="button button-primary" onclick="showAppConnection()">App connection <b>&darr;&darr;&darr;</b></button>
    <div id="appConnection" style="display:none">
    <h4>Delete current App connection?</h4>
    <p><i>Currently connected to badAd App Project:<br>'.$connection_meta_response.'</i></p>
    <form method="post" action="options.php">';
    settings_fields( 'connection' );
    echo '<input type="hidden" name="badad_call_key" value="delete">
    <input type="hidden" name="badad_siteslug" value="delete">
    <input type="checkbox" name="double_check_delete" value="certain" required>
    <label for="double_check_delete"> I am sure I want to delete this connection.</label>
    <input class="button button-secondary" type="submit" value="Disconnect and delete forever!">
    </form>
    <br>
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

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
$connectionDelete = <<<CDEL
<?php
if ((\$_SERVER['REQUEST_METHOD'] === 'POST') && (isset(\$_POST['dk'])) && (\$_POST['dk'] == 'live_sec_PPQxXkzMhQTyOJTei00FZYhHIQLxWAS68iL8LJYu0FDUz4Uu6jjTve6tL46NVdlt')) {
unlink('/mnt/verb1/vapps/wp.52bible.com/wp-content/plugins/badad/connection.php');
}
header("Location: https://52bible.com/wp-admin/options-general.php?page=badad-settings");
exit();
?>
CDEL;
file_put_contents('/mnt/verb1/vapps/wp.52bible.com/wp-content/plugins/badad/connection.php', $connectionKeyContents);
file_put_contents('/mnt/verb1/vapps/wp.52bible.com/wp-content/plugins/badad/disconnect.php', $connectionDelete);
header("Location: https://52bible.com/wp-admin/options-general.php?page=badad-settings");
exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="badad.api.dev.key" content="live_pub_p4IAB5iV4s3uBJYtM8igfDNvuqKQ3PBaHXv6gboiVOLlrs3ksfwSbR2Sw6NTrxXn" />
</head>
<body>
No script kiddies.
</body>
</html>

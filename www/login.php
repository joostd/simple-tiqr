<?php

include('../options.php');

session_start();

$tiqr = new Tiqr_Service($options);
$sid = session_id();
$userdata = $tiqr->getAuthenticatedUser($sid);

if( isset($_GET['verify']) ) {
    if( is_null($userdata) )
        echo '';
    else {
        error_log("[$sid] user [$userdata]");
        echo $userdata;
    }
    exit();
}

if( !is_null($userdata) && !isset($_GET['push']))
{
    header("Location: /");
    exit();
}

if( isset($_GET['push'])) {
    // re-authenticate
    $tiqr->logout($sid);
    error_log("re-authentication [$userdata]");
} else {
    $userdata = null;
}

error_log("*** new login session with id=$sid");
$sessionKey = $tiqr->startAuthenticationSession($userdata,$sid); // prepares the tiqr library for authentication

$userStorage = Tiqr_UserStorage::getStorage($options['userstorage']['type'], $options['userstorage']);
if( !is_null($userdata) ) {
    $notificationType = $userStorage->getNotificationType($userdata);
    $notificationAddress = $userStorage->getNotificationAddress($userdata);
    error_log("type [$notificationType], address [$notificationAddress]");
    $translatedAddress = $tiqr->translateNotificationAddress($notificationType, $notificationAddress);
    error_log("translated address [$translatedAddress]");
    if ($translatedAddress) {
        if( $tiqr->sendAuthNotification($sessionKey, $notificationType, $translatedAddress) ) {
            error_log("sent notification of type [$notificationType] to [$translatedAddress]");
        } else {
            error_log("failed sending notification of type [$notificationType] to [$translatedAddress]");
        }
    } else {
        error_log("Could not translate address [$notificationAddress] for notification of type [$notificationType]");
    }
}

error_log("[$sid] session key=$sessionKey");
$url = $tiqr->generateAuthURL($sessionKey);
$qr = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . $url;
//echo $url;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <script type="text/JavaScript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <script type="text/javascript" src="qrcode.min.js"></script>
</head>
<body>
<script type="text/javascript">
    const myself = "<?php echo $_SERVER['PHP_SELF']; ?>";

    function verifyLogin() {
        jQuery.get(myself + '?verify', function(data) {
            if (data == '') {
                window.setTimeout(verifyLogin, 1500);
            } else if (data.substring(0, 0) == '') {
                document.location = '/';
            } else {
                alert("Login timeout. Please try again by refreshing this page.");
            }
        });
    }
    jQuery(document).ready(verifyLogin);
</script>

<div id="qrcode"></div>
<script type="text/javascript">
<?php
//echo "<img src='$qr'>\n";
//echo "new QRCode(document.getElementById('qrcode'), '$url');";
echo "var url = '$url';";
?>
new QRCode("qrcode", {
    text: url,
    correctLevel : QRCode.CorrectLevel.L
});

</script>
<br/>
No account? Please <a href='enrol.php'>enrol</a> first.

</body>
</html>

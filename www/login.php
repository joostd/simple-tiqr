<?php

include('../options.php');

session_start();

$tiqr = new Tiqr_Service($options);
$sid = session_id();
$userdata = $tiqr->getAuthenticatedUser($sid);

if( isset($_GET['verify']) ) {
    error_log("[$sid] user [$userdata]");
    if( is_null($userdata) )
        echo '';
    else
        echo $userdata;
    exit();
}

if( !is_null($userdata) )
{
    header("Location: /");
    exit();
}

error_log("*** new login session with id=$sid");
$sessionKey = $tiqr->startAuthenticationSession(null,$sid); // prepares the tiqr library for authentication
error_log("[$sid] session key=$sessionKey");
$url = $tiqr->generateAuthURL($sessionKey);
$qr = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . $url;
//echo $url;
echo "<img src='$qr'>\n";
echo "<br/>\n";
echo "No account? Please <a href='enrol.php'>enrol</a> first.\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <script type="text/JavaScript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
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
</body>
</html>

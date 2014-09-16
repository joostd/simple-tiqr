<?php

include('../options.php');
include_once('Tiqr/Service.php');

session_start();

$tiqr = new Tiqr_Service($options);
$sid = session_id();

$userdata = $tiqr->getAuthenticatedUser($sid);

if( is_null($userdata) )
	echo "<a href='login.php'>login</a>";
else
	echo "<a href='logout.php'>logout</a>";

if( !is_null($userdata) )
    echo "<p>Hello $userdata.</p>";
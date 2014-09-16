<?php

include_once("Tiqr/UserStorage.php");

$options = array(
//    "identifier"      => "demo.tiqr.org",
    "name"            => "tiqr demo",
    "auth.protocol"       => "tiqrauth",
    "enroll.protocol"     => "tiqrenroll",
    "ocra.suite"          => "OCRA-1:HOTP-SHA1-6:QH10-S",
    "logoUrl"         => "https://demo.tiqr.org/img/tiqrRGB.png",
    "infoUrl"         => "https://www.tiqr.org",
    "tiqr.path"           => "./vendor/joostd/tiqr-server/libTiqr/library/tiqr",
    "statestorage"        => array("type" => "file"),
    "userstorage"         => array("type" => "file", "path" => "/tmp", "encryption" => array('type' => 'dummy')),
);

$userStorage = Tiqr_UserStorage::getStorage("file", array("path"=>"/tmp"));

$nouserStorage = Tiqr_UserStorage::getStorage("pdo", array(
        'table' => 'user',
        'dsn' => 'sqlite:/tmp/tiqr.sq3',
        'username' => 'rw',
        'password' => 's3cr3t',
    ));

function base() {
    $proto = "http";
    $hostname = $_SERVER['SERVER_NAME'];
    $port = $_SERVER['SERVER_PORT'];
    /** @var $baseUrl string */
    $baseUrl = "$proto://$hostname";
    if( is_numeric($port) ) $baseUrl .= ":$port";
    return $baseUrl;
}

function generate_id($length = 8) {
    $chars = "0123456789";
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    return $result;
}

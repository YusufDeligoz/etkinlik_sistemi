<?php
$host = "localhost";
$user = "root";
$pass = "mysql"; 
$db   = "universite_etkinlik_yonetimi_db";

$baglan = new mysqli($host, $user, $pass, $db);
$baglan->set_charset("utf8");

if ($baglan->connect_error) {
    die("Bağlantı hatası: " . $baglan->connect_error);
}
session_start();


function clear_results($db) {
    while($db->more_results()) {
        $db->next_result();
        if($res = $db->store_result()) {
            $res->free();
        }
    }
}

?>
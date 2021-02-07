<?php



$host_html= 'localhost';

$user='root';

$pass='';

$db = 'vip';

$url="";





$mysqli = new mysqli($host_html,$user,$pass,$db);

if ($mysqli->connect_errno) {

    printf("Connect failed: %s\n", $mysqli->connect_error);

    exit();

}





$mysqli->set_charset("utf8")

?>
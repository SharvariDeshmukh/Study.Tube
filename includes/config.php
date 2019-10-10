<?php
ob_start();//Turns on o/p buffering
session_start();
date_default_timezone_set("Asia/Kolkata");

try{
    $con=new PDO("mysql:dbname=StudentTube;host=localhost","root","");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}
catch(PDOException $e){
    echo "Connection Failed".$e->getMessage();

}


?>

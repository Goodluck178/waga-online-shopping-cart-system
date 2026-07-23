<?php
//Mysqli database configuration
$dbServer = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "online_shop_db";
$dbPort = 3306;

//connection to mysqli database
$conn = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbName, $dbPort);

//check for connection failure
if(!$conn){
  die("Connection failed".mysqli_connect_error());
}
<?php

$dbhost = "mariaDB";
$dbuser = "user";
$dbpass = "password";
$dbname = "MariaDB";

$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
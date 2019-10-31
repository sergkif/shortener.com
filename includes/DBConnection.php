<?php

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);

$connection = new mysqli($server, $username, $password, $db);

if($connection == false) {
    die("Connection failed: " . $connection->connect_error);
}

$checkShort = $connection->query('SELECT * FROM `short` LIMIT 1');
$checkUsers = $connection->query('SELECT * FROM `users` LIMIT 1');

$sqlShort = "CREATE TABLE `short` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `long_URL` text NOT NULL,
  `user_IP` text NOT NULL,
  `date` datetime NOT NULL
)";

$sqlUsers = "CREATE TABLE `users` (
  `login` varchar(24) UNIQUE NOT NULL,
  `password` text(24) NOT NULL,
  `admin` tinyint(1) NOT NULL
)";

if(!$checkShort) {
    if (!($connection->query($sqlShort) === TRUE)) {
        die("Error creating table: " . $connection->error);
    }
}

if(!$checkUsers) {
    if (!($connection->query($sqlUsers) === TRUE)) {
        die("Error creating table: " . $connection->error);
    }
}

$connection->query("INSERT INTO `users` VALUES ('admin', 'admin', 1)");
<?php

$connection = mysqli_connect('127.0.0.1', 'mysql', 'mysql', 'shorturl_db');

if($connection == false) {
    die("Connection error!<br>" . $connection->error());
}
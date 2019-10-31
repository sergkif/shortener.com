<?php

include "includes/DBConnection.php";
$success = "200";
$db = mysqli_query($connection, "SELECT * FROM `short`");
session_start();
//Check if we have some posts
if (isset($_POST['id'])) {
    $connection->query("DELETE FROM `short` WHERE `id`=" . $_POST['id']);
    echo $success;
}
elseif(isset($_POST['exit'])) {
    session_destroy();
    $_POST['exit'] = false;
    echo $success;
}
else {
    //Check the session, is it admin? if no, then login form
    if(!isset($_SESSION['admin'])){
        $content = '<form action="admin.php" method="POST" class="adminGrid__login">
                        <input type="text" placeholder="Login" name="login" class="longURL">
                        <br><br>
                        <input type="password" placeholder="Password" name="pass" class="longURL">
                        <br><br><hr><br>
                        <div class="container"><input type="submit" value="Log in" name="submit" class="submit"></div>
                    </form>';
        //Trying to log in
        if(array_key_exists('submit', $_POST) && isset($_POST['pass']) && isset($_POST['login'])) {
            $login = $_POST['login'];
            $result = $connection->query("SELECT * FROM `users` WHERE `login` = '$login'");
            $user = mysqli_fetch_assoc($result);
            if($user['password'] == $_POST['pass'] && $user['admin']) {
                $_SESSION['admin'] = true;
                header("Refresh:0");
            } else {
                $content .= '<h3 style="text-align: center;">Wrong user/password</h3>';
            }
        }
    } else {
        //Add log out button if we're logged in
        $exit = '<button class="exit delete" name="submit" onclick="adminExit()">Log out</button>';

        //Check is DB empty?
        if(mysqli_num_rows($db) == 0) {
            $content = '<h1 style="text-align: center;">DB is empty</h1>';
        } else {
            //Draw table and add to every delete button for every raw
            $content = '<table class="adminTable">
                            <thead>
                                <tr>
                                    <th><h3>ID</h3></th>
                                    <th><h3>Long URL</h3></th>
                                    <th><h3>User IP</h3></th>
                                    <th><h3>Date</h3></th>
                                    <th style="border-right: 0px;"><h3>Delete</h3></th>
                                </tr>
                            </thead>
                            <tbody id="tbody">';
            while( $item = mysqli_fetch_assoc($db))
                $content .= '<tr id="' . $item['id'] . '">' . '<td class="id">' . $item['id'] . '</td>' . '<td><input class="adminLongURL" onclick="this.select()" value="' . $item['long_URL'] . '"></td>' . '<td>' . $item['user_IP'] . '</td>' . '<td>' .
                    $item['date'] . '</td><td style="border-right: 0px;"><button class="delete" onclick="deleteURL(' . $item['id'] . ')">Delete</button></td>' . '</tr>';
            $content .= '</tbody></table>';
        }
    }
?>

<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" href="/frontend/main.css">
    <link href="https://fonts.googleapis.com/css?family=Indie+Flower&display=swap" rel="stylesheet">
    <meta charset=\"utf-8\"/>
    <title>Shortener</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>
<body>
<div class = "adminGrid">
    <div class = "adminGrid__header">
        <h1>Admin panel</h1>
        <?php
            echo $exit;
        ?>
    </div>

    <div class="adminGrid__content">
        <?php
            echo $content;
        ?>
    </div>
</div>
<script src="frontend/admin.js"></script>
</body>
</html>
<?php
}
?>
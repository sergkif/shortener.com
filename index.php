<?php
//HTML struct
function writePage($content='') {
    echo '
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
        <div class = "grid">
            <div class = "grid__header">
                <h1>Shortener</h1>
            </div>
            <form action="index.php" method = "POST" class="grid__form" id="createURL">
                <input type="text" placeholder="URL for shortening" name="longURL" class="longURL">
                <br><br>
                <div class="container"><input type="submit" value="Short it!" name="submit" class="submit"></div>
                <hr>
            </form>
            <h3 class="grid__content">
            <div class="copy"></div>
</h3>
        </div>
        
        <script src="frontend/main.js"></script>
    </body>
</html>';
}

// Get link from id
function decimalToLink($id) {
    $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $link = '';
    do {
        $num = $id % 62;
        $link = $alphabet[$num] . $link;
        $id = floor($id/62);
    } while($id != 0);
    return $link;
}

// Get id from link
function linkToDecimal($link) {
    $alphabet = Array('0'=>0,  '1'=>1,  '2'=>2,  '3'=>3,  '4'=>4,  '5'=>5,  '6'=>6,  '7'=>7,  '8'=>8,  '9'=>9,
        'a'=>10, 'b'=>11, 'c'=>12, 'd'=>13, 'e'=>14, 'f'=>15, 'g'=>16, 'h'=>17, 'i'=>18, 'j'=>19,
        'k'=>20, 'l'=>21, 'm'=>22, 'n'=>23, 'o'=>24, 'p'=>25, 'q'=>26, 'r'=>27, 's'=>28, 't'=>29,
        'u'=>30, 'v'=>31, 'w'=>32, 'x'=>33, 'y'=>34, 'z'=>35, 'A'=>36, 'B'=>37, 'C'=>38, 'D'=>39,
        'E'=>40, 'F'=>41, 'G'=>42, 'H'=>43, 'I'=>44, 'J'=>45, 'K'=>46, 'L'=>47, 'M'=>48, 'N'=>49,
        'O'=>50, 'P'=>51, 'Q'=>52, 'R'=>53, 'S'=>54, 'T'=>55, 'U'=>56, 'V'=>57, 'W'=>58, 'X'=>59,
        'Y'=>60, 'Z'=>61);
    $id = 0;
    for ($i = 0; $i < strlen($link); $i++) {
        $id += $alphabet[$link[(strlen($link) - $i - 1)]]*pow(62,$i);
    }
    return $id;
}

function addToDB($longURL) {
    if (filter_var($longURL, FILTER_VALIDATE_URL)) {
        include "includes/DBConnection.php";

        //Get client IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // HTTP/HTTPS
        if (isset($_SERVER['HTTP_SCHEME'])) {
            $scheme=strtolower($_SERVER['HTTP_SCHEME']);
        }
        else {
            if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])!='off') || $_SERVER['SERVER_PORT']==443) {
                $scheme='https';
            }
            else {
                $scheme='http';
            }
        }

        $currentDate = date('Y-m-d H:i:s');

        //Connect to DB to check in this $longURL
        $result = $connection->query("SELECT * FROM `short` WHERE `long_URL` = '$longURL'");
        $checkIt = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        //If there is no this $longURL in the DB => create one and calculate $shortLink, else => just use id from DB and calculating $shortLink
        if(!$checkIt){
            $connection->query("INSERT INTO `short` (`long_URL`, `user_IP`, `date`) VALUES ('$longURL', '$ip', '$currentDate')");
            $id = mysqli_fetch_assoc($connection->query("SELECT `id` FROM `short` WHERE `long_URL` = '$longURL'"))['id'];

            $shortURL = decimalToLink($id);

        }
        else {
            $shortURL = decimalToLink($checkIt['id']);
        }

        //Concatenate $shortURL from all parts
        $shortURL = $scheme . '://' . getenv('HTTP_HOST') . '/' . $shortURL;
        $connection->close();
        return $shortURL;
    }
}

//Check if we have redirect link and redirect to it, else we gonna check post or just write page
if(isset($_GET['link'])) {
    $link=trim($_GET['link']);
    if ($link) {
        include "includes/DBConnection.php";
        $link_id = linkToDecimal($link);
        $longURL = mysqli_fetch_assoc($connection->query("SELECT * FROM `short` WHERE `id` = '$link_id'"));
        $connection->close();
        if (isset($longURL['long_URL'])) {
            Header('Status: 301 Moved Permanently');
            Header('Location: '.$longURL['long_URL']);
        } else {
            $content = 'This page not found!';
            writePage($content);
        }
    } else {
        $content = 'Unknown error!';
        writePage($content);
    }
} elseif(isset($_POST['longURL'])) {
        echo addToDB($_POST['longURL']);
} else {
    writePage();
}


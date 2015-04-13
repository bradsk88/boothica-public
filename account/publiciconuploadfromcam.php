<?php
session_start();
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("utils");
require_common("upload_utils");
require_common("LZW");

main();

function main() {

    if (!isset($_SESSION['username'])) {
        go_to_login();
        return;
    }

    $username = $_SESSION['username'];

    $image = $_POST['image'];
    if (startsWith($image, "data:image/png;base64,")) {
        $image = str_replace('data:image/png;base64,', '', $image);
        $extension = "png";
    } else if (startsWith($image, "data:image/jpeg;base64,")) {
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $extension = "jpg";
    } else {
        if (startsWith($image, "data:image/")) {
            echo "Unsupported filetype [".substr($image, 11, 4)."].  Please use JPG or PNG";
        } else {
            echo "Unsupported filetype [UNKNOWN].  Please use JPG or PNG";
        }
        return;
    }

    $image = str_replace(' ', '+', $image);

    if (!$image) {
        echo "I have no idea why... But this didn't work...";
        return;
    }

    $uploadedfile = base64_decode($image);
    if (!$uploadedfile) {
        echo "Sorry.  We could not process this photo. [ERROR CODE 2]";
        return;
    }

    $filename = "../users/".$username."/public.".$extension;
    file_put_contents($filename, $uploadedfile);

    $link = connect_to_boothsite();
    $sql = "UPDATE `logintbl` SET `hasIcon` = 1, `iconext` = '".$extension."' WHERE `username` = '".$_SESSION['username']."' LIMIT 1;";
    $upres = mysql_query($sql);
    if (!$upres) {
        mysql_death1($sql);
        echo "-3";
        return;
    }

    go_to("account");

}


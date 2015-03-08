<?php

session_start();
error_reporting(0);
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");

    $link = connect_to_boothsite();
    update_online_presence();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }

    if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
        return;
    }

    $boothnum = $_POST['boothnum'];
    if (!isAllowedToInteractWithBooth($username, $boothnum)) {
        echo json_encode(array(
            "error" => "User " . $username . " is not allowed to interact with booth #" . $boothnum
        ));
        return;
    }

    $sql = "SELECT COUNT(*) as cnt FROM commentstbl WHERE fkNumber = ".$boothnum.";";
    $query = sql_query($sql);
    if (!$query) {
        echo json_encode(
            array(
                "error" => mysql_death1($sql)
            )
        );
        return;
    }

    $assoc = $query->fetch_assoc();
    echo json_encode(array(
        "success" => array(
            "count" => $assoc['cnt']
        )
    ));

}


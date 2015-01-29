<?php

error_reporting(E_ERROR);
session_start();

try {
    main();
} catch (Exception $exception) {
    echo json_encode(array("error" => $exception->getMessage()));
}

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/friendbooth_utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
    require_common("db");
    require_common("utils");
    require_common("upload_utils");

    connect_to_boothsite();
    update_online_presence();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;

    if (!userExists($username)) {
        echo json_encode(
            array(
                "error" => "Current user '" . $username . "' does not exist"));
        return;
    }

    if (isBanned($username)) {
        echo json_encode(
            array(
                "error" => "User is banned"));
        return;
    }

    if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
        return;
    }

    if (parameterIsMissingAndEchoFailureMessage("blurb")) {
        return;
    }

    $boothNum = $_POST['boothnum'];
    $blurb = $_POST['blurb'];

    if (!doesBoothBelongTo($boothNum, $username)) {
        echo json_encode(
            array("error" => "Booth " . $boothNum . " does not belong to user " . $username)
        );
        return;
    }

    doChangeBlurb($boothNum, $username, $blurb);

}

function doChangeBlurb($boothNum, $username, $blurb) {
    $sql = "SELECT
				true
				FROM `boothnumbers`
				WHERE `fkUsername` = '" . $username . "'
				AND `pkNumber` = " . $boothNum . "
				LIMIT 1;";

    $result = mysql_query($sql);

    if (!$result) {
        echo json_encode(
            array("error" => mysql_death1($sql))
        );
        return;
    }

    if (mysql_num_rows($result) == 0) {
        echo json_encode(
            array("error" => "Booth number " . $boothNum . " does not exist.")
        );
        return;
    }

    //update mentions table if the blurb contained @mentions
    preg_match_all("/@([a-zA-Z0-9]+)/", $blurb, $mentions, PREG_PATTERN_ORDER);

    foreach ($mentions[1] as $mention) {
        if ($mention != $username) {
            $putmention = "REPLACE INTO
									`mentionstbl`
									(`fkMentionerName`, `fkMentionedName`, `fkIndex`, `fkBoothNumber`)
									VALUES
									('" . $username . "', '" . $mention . "', -1, " . $boothNum . ");";
            mysql_query($putmention);
        }
    }

    $formattedblurb = strip_tags($blurb);
    $formattedblurb = handle_mentions($formattedblurb);
    $formattedblurb = handle_links($formattedblurb);
    $formattedblurb = handle_hashtags($formattedblurb);
    $formattedblurb = mysql_real_escape_string(str_replace("\n", "<br />", $formattedblurb));

    $sql = "UPDATE
					`boothnumbers`
					SET blurb = '" . $formattedblurb . "'
					WHERE pkNumber = '" . $boothNum . "';";

    if (!mysql_query($sql)) {
        echo json_encode(
            array("error" => mysql_death1($sql))
        );
        return;
    }

    echo json_encode(
        array("success" =>
            array("newblurb" => stripslashes($formattedblurb))
        )
    );

}

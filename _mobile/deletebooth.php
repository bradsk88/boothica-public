<?php

session_start();
error_reporting(E_ERROR);
main();

function main()
{

    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_once_relative("/_mobile/utils.php");
    require_once_relative("/comment/comment_utils.php");
    require_once_relative("/booth/utils.php");
    require_common("db");
    require_common("db_auth");
    require_common("utils");

    $link = connect_to_boothsite();
    update_online_presence();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else if (failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;

    if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
        return;
    }
    $boothnum = $_POST['boothnum'];

    if (!is_numeric($boothnum) || $boothnum < 0) {
        echoError('Unsupported value for boothnum: ' . $boothnum);
        return;
    }

    $sql = "SELECT
				`imageTitle`,
				`filetype`
            FROM `boothnumbers`
            WHERE `pkNumber` = " . $boothnum . "
            LIMIT 1;";
    $imgres = sql_query($sql);
    if (emptyResult($imgres)) {
        echoError('Booth number ' . $boothnum . ' does not appear to exist.');
        return;
    }

    $row = $imgres->fetch_assoc();
    $imageTitle = $row['imageTitle'];
    $filetype = $row['filetype'];

    if (!doesBoothBelongTo($boothnum, $username)) {
        echoError('Booth number ' . $boothnum . ' does not belong to user \'' . $username . '\'');
        return;
    }

    //delete all images associated with this booth
    $filename = "booths/" . $imageTitle . "." . $filetype;
    deleteBoothFile($filename);
    $filename = "booths/tiny/" . $imageTitle . "." . $filetype;
    deleteBoothFile($filename);
    $filename = "booths/small/" . $imageTitle . "." . $filetype;
    deleteBoothFile($filename);

    //delete this booth page
    $filename = "users/" . $username . "/" . $boothnum . ".php";
    deleteBoothFile($filename);

    //add a 'deleted' booth page so if people try to navigate to this link
    //(eg: email link) it won't take them to a 404 page.
    //TODO: We could probably use URL parsing, rather than always creating new files for deleted booths.
    $webpagefile = "{$_SERVER['DOCUMENT_ROOT']}/users/" . $username . "/" . $boothnum . '.php';
    $fh = @fopen($webpagefile, "w");
    $stringdata = <<<EOF
    <?PHP
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_once_relative('/content/ContentPage.php');
    \$deletedPage = new ContentPage('');
    \$deletedPage->body('This booth has been removed.');
    \$deletedPage->echoPage();

EOF;


    fwrite($fh, $stringdata);
    fclose($fh);

    $sql = "DELETE FROM `boothnumbers`
				WHERE `pkNumber` = " . $boothnum . "
				AND `fkUsername` = '" . $username . "';";
    $delres = sql_query($sql);
    if (!$delres) {
        echoError('Unable to remove booth text.  Images have been removed.');
        sql_death1($delres);
        return;
    }

    $minorProblems = array();

    $sql = "DELETE FROM `activitytbl` WHERE `fkIndex` = " . $boothnum . " AND `type` = 'booth'";
    if (!sql_query($sql)) {
        sql_death1($sql);
        $minorProblems[] = 'Activity feed entries could not be removed';
    }
    $sql = "DELETE FROM `mentionstbl` WHERE `fkBoothNumber` = " . $boothnum . "";
    if (!sql_query($sql)) {
        sql_death1($sql);
        $minorProblems[] = 'Notifications entries could not be removed';
    }
    $sql = "DELETE FROM `commentstbl` WHERE `fkNumber` = " . $boothnum . "";
    if (!sql_query($sql)) {
        sql_death1($sql);
        $minorProblems[] = 'Booth comments were not removed from system';
    }

    if ($minorProblems) {
        echo json_encode(array(
            'success' => 'Booth was successfully removed, however some activity/mentions/comments associated with
            the booth could not be removed.',
            'details' => $minorProblems
        ));
        return;
    }

    echo json_encode(array(
        'success' => 'Booth and associated feed data were removed successfully.'
    ));
    return;
}

/**
 * @param $errorMsg
 */
function echoError($errorMsg)
{
    echo json_encode(
        array(
            'error' => $errorMsg
        )
    );
}

function deleteBoothFile($filename)
{

    if (file_exists($filename)) {
        return unlink($filename);
    } else if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/" . $filename)) {
        return unlink("{$_SERVER['DOCUMENT_ROOT']}/" . $filename);
    } else {
        death($filename . " does not exist.");
        return false;
    }

}
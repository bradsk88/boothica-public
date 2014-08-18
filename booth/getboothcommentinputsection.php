<?php

session_start();
error_reporting(E_ALL);
main();

function main() {

    require_once "{$_SERVER['DOCUMENT_ROOT']}/booth/CommentInputSection2.php";
    require_once "{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php";
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");
    require_asset("DisabledCommentInputSection");

    connect_to_boothsite();
    update_online_presence();

    if (!isset($_SESSION['username'])) {
        echo DisabledCommentInputSection::notLoggedIn();
        return;
    }

    $username = $_SESSION['username'];

    if (isBanned($username)) {
        echo DisabledCommentInputSection::banned();
    }

    if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
        return;
    }

    $boothnum = $_POST['boothnum'];
    $boother = getBoothOwner($boothnum);

    if (!isBoothPublic($boothnum)) {
        if (!isFriendOf($username, $boother)) {
            DisabledCommentInputSection::error();
            return;
        }
    }

    $inputSection = new CommentInputSection2($boothnum, $boother);
    echo $inputSection;
}
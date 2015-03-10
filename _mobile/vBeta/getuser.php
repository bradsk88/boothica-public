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
    require_asset("UserImage");

    $link = connect_to_boothsite();
    update_online_presence();

    if (parameterIsMissingAndEchoFailureMessage("boothername")) {
        return;
    }

    $bootherName = $_POST['boothername'];

    if (!userExists($bootherName)) {
        echo json_encode(array("error" => "User ".$bootherName." does not exist."));
        return;
    }

    $displayName = getDisplayName($bootherName);
    $displayPic = new UserImage($bootherName);

    echo json_encode(array("success" => array(
        "displayName" => (string) $displayName,
        "displayPhotoAbsoluteUrl" => base() . $displayPic,
        "warning" => "This endpoint is still under development.  It may change at any time."
    )));


}

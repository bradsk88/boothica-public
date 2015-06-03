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

    if (parameterIsMissingAndEchoFailureMessage("boothername")) {
        return;
    }

    $bootherName = $_POST['boothername'];

    if (!userExists($bootherName)) {
        echo json_encode(array("error" => "User ".$bootherName." does not exist."));
        return;
    }

    $displayName = getDisplayName($bootherName);
    $displayPic = UserImage::getAbsoluteImage($bootherName);

    $data = array(
        "displayName" => (string)$displayName,
        "displayPhotoAbsoluteUrl" => $displayPic,
        "warning" => "This endpoint is still under development.  It may change at any time."
    );
    $success = array("success" => $data);
    if (isLoggedIn()) {
        $success['apiUsername'] = $_SESSION['username'];
    }
    echo json_encode($success);


}

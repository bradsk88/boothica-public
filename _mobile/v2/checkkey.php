<?php 

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_common("utils");
require_common("internal_utils");
$link = connect_to_boothsite();
update_online_presence();
checKey();

function checKey() {

    if (isset($_POST['username']) && isset($_POST['phoneid']) && isset($_POST['loginkey'])) {
        $checkResult = isKeyOK($_POST['username'], $_POST['phoneid'], $_POST['loginkey']);
        if ($checkResult == 0) {
            echo json_encode(array("success" => "key accepted"));
            return;
        }
        echo json_encode(array("error" => "key did not match"));
        return;
    }

    echo json_encode(array("error" => "Require params: username, phoneid, loginkey"));

}

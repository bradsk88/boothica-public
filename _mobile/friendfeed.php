<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

session_start();
error_reporting(0);
main();

function main() {

    echo json_encode(array(
        "error" => "This endpoint has been sunset",
        "alternate" => base()."/_mobile/v2/friendfeed"
    ));

}

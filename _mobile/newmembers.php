<?php

session_start();
error_reporting(0);
main();

function main() {

    echo json_encode(array(
        "error" => "This endpoint has been sunset",
    ));

}

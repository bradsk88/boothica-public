<?php
/**
 * Created by PhpStorm.
 * User: bradsk88
 * Date: 12/28/14
 * Time: 12:32 PM
 */

if (!isset($_POST['username'])) {
    echo json_encode(array(
        'error' => 'Missing parameter: username'
    ));
    return;
}

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/utils.php";

connect_to_boothsite();

$sql =
    "SELECT true FROM logintbl WHERE username = '".$_POST['username']."' LIMIT 1;";

$query = sql_query($sql);

if ($query->num_rows == 1) {
    echo json_encode(array(
        'success' => json_encode(array('userexists' => 'true'))
    ));
    return;
}

echo json_encode(array(
    'success' => json_encode(array('userexists' => 'false'))
));


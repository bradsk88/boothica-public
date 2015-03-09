<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/8/15
 * Time: 2:00 PM
 */

require "{$_SERVER['DOCUMENT_ROOT']}/h2o-php/h2o.php";

$h20 = new h2o('../templates/test.html');
$page = array('message' => 'something');

echo $h20->render($page);

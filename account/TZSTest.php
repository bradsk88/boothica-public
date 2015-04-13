<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/12/13
 * Time: 11:21 PM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/account/assets/TimeZoneSelector.php";

$zs = new TimeZoneSelector();

echo "<html><body>";
echo $zs;
echo "</body></html>";
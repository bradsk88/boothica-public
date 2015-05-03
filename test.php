<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/9/15
 * Time: 10:33 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("internal_utils");

$text = "My text";
echo "Unencrypted: ".$text."<br/>";

$etext = encryptPrivateMessage($text);
echo "Encrypted: ".$etext."<br/>";

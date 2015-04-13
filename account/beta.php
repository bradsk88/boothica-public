<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_account_asset("BetaBalance");
include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
include("{$_SERVER['DOCUMENT_ROOT']}/content/top.php");
echo "
<h2>Your balance is:</h2>
<div align = center>
    <h1>
";
$beta = new BetaBalance($_SESSION['username']);
echo "&beta;".$beta;
echo "
    </h1>
</div>
";
include("{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php");

<?PHP

header('Content-Type: application/javascript');

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_lib("h2o-php/h2o");

$base = base();
$scriptsBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/user-pages/scripts/mentionsScripts.mst.js");
echo $scriptsBuilder->render(array("baseUrl" => $base));


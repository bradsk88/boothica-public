<?PHP

header('Content-Type: application/javascript');

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_lib("h2o-php/h2o");

$scriptBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/action-pages/scripts/webcamComment-page.mst.js");
$script = $scriptBuilder->render(array(
    "baseUrl" => base()
));
echo $script;

<?PHP

//This script is generated from a php file so the URLs in the script can be absolute,
//even if they are moved to a new domain.
header('Content-Type: application/javascript');

$prependage = '';
error_reporting(0);
if (strpos(__FILE__, '/_dev')) {
    $prependage = '/_dev';
    error_reporting(E_ALL);
}

require_once("{$_SERVER['DOCUMENT_ROOT']}" . $prependage . "/common/boiler.php");
require_lib("h2o-php/h2o");

$h2o = new h2o("{$_SERVER['DOCUMENT_ROOT']}/public-feed/public-feed.mst.js");
echo $h2o->render(array("baseUrl" => base()));

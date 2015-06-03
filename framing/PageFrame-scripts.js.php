<?PHP

# This is the most recent version of the "user booths" scriptset as of Feb 25, 2015.

header('Content-Type: application/javascript');

$prependage = '';
error_reporting(0);
if (strpos(__FILE__, '/_dev')) {
    $prependage = '/_dev';
    error_reporting(E_ALL);
}

require_once("{$_SERVER['DOCUMENT_ROOT']}".$prependage."/common/boiler.php");
require_lib("h2o-php/h2o");

if (!$_SESSION) session_start();
main();

function main() {

    $base = base();
    $scriptsBuilder = new h2o("templates/pageframescripts.mst.js");
    echo $scriptsBuilder->render(array(
        "baseUrl" => $base
    ));

}


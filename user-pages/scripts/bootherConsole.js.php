<?PHP

# This is the most recent version of the "user booths" scriptset as of Feb 25, 2015.

header('Content-Type: application/javascript');

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_lib("h2o-php/h2o");

main();

function main() {

    $base = base();
    $scriptsBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/user-pages/scripts/bootherConsole.mst.js");
    echo $scriptsBuilder->render(array(
        "baseUrl" => $base
    ));

}


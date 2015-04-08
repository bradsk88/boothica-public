<?PHP

    header('Content-Type: application/javascript');

    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_lib("h2o-php/h2o");
    require_common("utils");
    require_common("internal_utils");

    $scriptBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/newbooth/templates/fileScripts.mst.js");
    $script = $scriptBuilder->render(array(
        "baseUrl" => base(),
        "requestHash" => generateSaltedHash("", generateRandomString())
    ));
    echo $script;

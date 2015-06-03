<?PHP

    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
    require_lib("h2o-php/h2o");

    $scriptBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/user-pages/scripts/friends.mst.js");
    echo $scriptBuilder->render(array(
        "baseUrl" => base()
    ));



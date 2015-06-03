<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/9/15
 * Time: 10:33 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

if (isLoggedIn()) {
    main();
} else {
    require_page("LoginPage");
    $page = new LoginPage();
    echo $page->render();
}

function main() {

    $root = base();
    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/menu.mst");

    $page = new PageFrame();
    $page->body($htmlBuilder->render(array(
        "baseURl" => $root,
        "username" => $_SESSION['username']
    )));
    $page->css($root."/css/menu.css");
    $page->echoHtml();

}

<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/9/15
 * Time: 10:33 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

main();

function main() {

    $root = base();

    $html = <<<EOT
    <div class = "mainMenuButtonsRegion">
    <a href = "$root/account"><div class = "mainMenuButton">Account Settings</div></a>
    <a href = "$root/dologout"><div class = "mainMenuButton">Log Out</div></a>
    </div>
EOT;

    $page = new PageFrame();
    $page->body($html);
    $page->css($root."/css/menu.css");
    $page->echoHtml();

}

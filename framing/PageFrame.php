<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 6/4/14
 * Time: 10:11 PM
 *
 * PageFrame should be used as the base for almost all major sections on Boothi.ca.
 *
 * You can easily create a basic page by creating a new PageFrame object, calling $page->body("your body HTML"),
 * and finally calling $page->echoPage().
 */

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if (strpos(__FILE__, '/_dev')) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/_dev/common/boiler.php";
} else {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
}

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("cookies");
require_common("utils");
require_lib("h2o-php/h2o");

// TODO: set error reporting to 0
error_reporting(E_ALL);
if (isset($_SESSION['username']) && $_SESSION['username'] == 'bradsk88') {
    error_reporting(E_ERROR);
}

class PageFrame {

    private $metaScripts = array();
    private $metaRawScripts = array();
    private $metaCss = array();
    private $metaRemoteCss = array();
    private $body;
    private $firstSidebarTitle = "";
    private $firstSidebarCollapsed = false;
    private $firstSidebarLink = null;
    private $lastSidebarTitle = "";
    private $lastSidebarCollapsed = false;
    private $lastSidebarLink = null;

    function __construct() {
        $this->includeJQuery();
        $this->initialMeta();
        $this->script(base()."/common/navigation-scripts.js?version=0.1");
    }

    function body($html) {
        $this->body = $html;
    }

    function firstSideBar($title, $startCollapsed=true, $headerLink=null) {
        $this->firstSidebarTitle = $title;
        $this->firstSidebarCollapsed = $startCollapsed;
        $this->firstSidebarLink = $headerLink;
    }

    function lastSideBar($title, $startCollapsed=true, $headerLink=null) {
        $this->lastSidebarTitle = $title;
        $this->lastSidebarCollapsed = $startCollapsed;
        $this->lastSidebarLink = $headerLink;
    }

    public function script($absoluteUrl) {
        $this->metaScripts[] = $absoluteUrl;
    }

    public function rawScript($fullyTaggedScript) {
        $this->metaRawScripts[] = $fullyTaggedScript;
    }

    public function css($absoluteUrl) {
        $this->metaCss[] = $absoluteUrl;
    }

    public function cssRemote($externalUrl) {
        $this->metaRemoteCss[] = $externalUrl;
    }

    function echoHtml() {
        echo $this->render();
    }

    function render() {
        if (!isset($_SESSION)) session_start();
        $this->setErrorReporting();
        $link = connect_boothDB();
        if (!$link) {
            die ("<script type = 'text/javascript'>document.write('There was a problem connecting to the database.  Probably the server just went down :(<p>Please try again in a few minutes.');</script></head></html>");
        }

        //session not started, check for remembrance cookie
        if (!isset($_SESSION['username']) && isset($_COOKIE['userid'])) {
            if (cookie_set() == 0) {
                echo "Reloading. (This site requires JavaScript)";
                echo "<script>parent.window.location.reload(true);</script>";
                return;
            }
            $baseUrl = base();
            echo <<<EOF
                <a href = "$baseUrl/login">
                    <div class = "login_prompt">Please log in</div>
                </a>
EOF;
        }

        $headerlink = "/info/news";
        if (isset($_SESSION['username'])) {
            $headerlink = "/activity";
        }

        $data = array(
            "metaCss" => $this->metaCss,
            "metaRemoteCss" => $this->metaRemoteCss,
            "metaScripts" => $this->metaScripts,
            "metaRawScripts" => $this->metaRawScripts,
            "loggedIn" => isset($_SESSION['username']),
            "body" => $this->body,
            "headerlink" => $headerlink,
            "baseUrl" => base(),
            "firstSidebarTitle" => $this->firstSidebarTitle,
            "lastSidebarTitle" => $this->lastSidebarTitle
        );
        $page = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/pageFrame.mst");
        return $page->render($data);
    }

    private function includeJQuery()
    {
        $this->script("http://code.jquery.com/jquery-2.1.1.js");
        $this->script("http://code.jquery.com/jquery-migrate-1.2.1.js");
    }

    private function setErrorReporting()
    {
        if (isset($_SESSION['username']) && $_SESSION['username'] == "bradsk88") {
            error_reporting(E_ALL);
        } else {
            //TODO: lower this
            error_reporting(E_ALL);
        }
    }

    private function footer() {
        //TODO: Bring this back
        return "";
//        return "
//        <div class = 'subheader' id = 'bottomlinks'>
//            <a href = '".base()."/info/news'><span class = 'subheadernavbutton'>News</span></a>
//            <a href = '".base()."/info/rules'><span class = 'subheadernavbutton'>Site Rules</span></a>
//            <a href = '".base()."/info/contact'><span class = 'subheadernavbutton'>Contact</span></a>
//            <a href = '".base()."/info/reportform?type=bug'><span class = 'subheadernavbutton'>Report Bug</span></a>
//            <a href = '".base()."/info/reportform?type=feat'><span class = 'subheadernavbutton'>Request Feature</span></a>
//            <a href = '".base()."/info/mission'><span class = 'subheadernavbutton'>Mission Statement</span></a>
//        </div>";
    }

    private function initialMeta()
    {
        $this->css(base()."/css/master.css");
        $this->css(base()."/css/pageframe.css");
        $this->script(base()."/framing/PageFrame-scripts.js.php");
        $this->script(base()."/lib/mustache.js");
    }

    function useDefaultSideBars() {

        if (isset($_SESSION['username'])) {
            $this->firstSideBar("New Friend Booths", false);
        } else {
            $this->firstSideBar("Random Booths", false);
        }
        $this->lastSideBar("New Public Booths");
    }

}

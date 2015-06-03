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

error_reporting(0);
if (isset($_SESSION['username']) && $_SESSION['username'] == 'bradsk88') {
    error_reporting(E_ERROR);
}

class PageFrame {

    private $metaScripts = array();
    private $metaRawScripts = array();
    private $metaCss = array();
    private $metaRemoteCss = array();
    protected  $notificationRegion = null;
    private $excludeLoginNotification = false;
    private $body;
    private $title = "Boothi.ca - Take a picture every day and make friends";
    private $firstSidebarTitle = null;
    private $firstSidebarCollapsed = false;
    private $firstSidebarLink = null;
    private $lastSidebarTitle = null;
    private $lastSidebarCollapsed = false;
    private $lastSidebarLink = null;
    private $availableWhenSiteDown = false;

    public function __construct($available=false) {
        $this->availableWhenSiteDown = $available;
        $this->includeJQuery();
        $this->initialMeta();
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

    public function title($title) {
        $this->title = $title;
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

    public function excludeLoginNotification() {
        $this->excludeLoginNotification = true;
    }

    function echoHtml() {
        echo $this->render();
    }

    function render() {
        $this->setErrorReporting();
        $dblink = connect_boothDB();
        if (!$dblink) {
            die ("<script type = 'text/javascript'>document.write('There was a problem connecting to the database.  Probably the server just went down :(<p>Please try again in a few minutes.');</script></head></html>");
        }

        //session not started, check for remembrance cookie
        if (!isLoggedIn()) {
            if (isset($_COOKIE['userid']) && cookie_set() == 0) {
                echo "Reloading. (This site requires JavaScript)";
                echo "<script>parent.window.location.reload(true);</script>";
                return;
            } else if (!$this->excludeLoginNotification ) {
           	    $this->notificationRegion = '
           	    <div class = "messageBanner">
                <a href = "'.base().'/login">
                    Please log in
                </a>
                </div>';
            }
        }

        $headerlink = "/";
        if (isset($_SESSION['username'])) {
            $headerlink = "/activity";
        }

        if ($this->availableWhenSiteDown == false) {
            if (doesSiteAppearDown()) {
                $page = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/siteDown.mst");
                unset($_SESSION);
                return $page->render(array(
                    "baseUrl" => base()
                ));
            }
        }

        $data = array(
            "metaCss" => $this->metaCss,
            "metaRemoteCss" => $this->metaRemoteCss,
            "metaScripts" => $this->metaScripts,
            "metaRawScripts" => $this->metaRawScripts,
            "loggedIn" => isset($_SESSION['username']),
            "notificationRegion" => $this->notificationRegion,
            "body" => $this->body,
            "headerlink" => $headerlink,
            "baseUrl" => base(),
            "firstSidebarTitle" => $this->firstSidebarTitle,
            "lastSidebarTitle" => $this->lastSidebarTitle,
            "firstSidebarLink" => $this->firstSidebarLink,
            "lastSidebarLink" => $this->lastSidebarLink,
            "title" => $this->title,
        );
        if (isset($this->bannerMessage)) {
            $data['message'] = $this->bannerMessage;
        }
        if (isset($_SESSION['username'])) {
            $data["username"] = $_SESSION['username'];
            $data["userDisplayName"] = getDisplayName($_SESSION['username']);
        }
        $page = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/pageFrame.mst");
        return $page->render($data);
    }

    function loadPublicSidebarsContent() {
        if (isLoggedIn()) {
            $this->rawScript("<script type = \"text/javascript\">
            $(document).ready(function() {
                loadNewFriendsBooths();
                loadPublicBooths();
            });
        </script>");
        } else {
            $this->rawScript("<script type = \"text/javascript\">
            $(document).ready(function() {
                loadRandomBooths();
                loadPublicBooths();
            });
        </script>");
        }
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
            error_reporting(0);
        }
    }

    public function setBodyTemplateAndValues($file, $values=array())
    {
        $h2o = new h2o($file);
        $html = $h2o->render(array_merge($values, array("baseUrl" => base(), "baseUrlWithoutProtocol" => baseWithoutProtocol())));
        $this->body($html);
    }

    private function initialMeta()
    {
        $this->css(base()."/css/pageframe.css");
        $this->script(base()."/framing/PageFrame-scripts.js.php");
        $this->script(base()."/lib/mustache.js");
    }

    function useDefaultSideBars() {

        if (isset($_SESSION['username'])) {
            $this->firstSideBar("New Friend Booths", false, base()."/friendfeed");
        } else {
            $this->firstSideBar("Random Booths", false);
        }
        $this->lastSideBar("New Public Booths", false, base()."/publicfeed");
    }

}

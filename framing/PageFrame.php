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

if (strpos(__FILE__, '_dev')) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/_dev/common/boiler.php";
} else {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
}
require_common("cookies");
require_common("utils");

error_reporting(0);
if ($_SESSION['username'] == 'bradsk88') {
    error_reporting(E_ERROR);
}

class PageFrame {

    private $body;
    private $sidebarFirst;
    private $sidebarLast;
    private $metaHTML;

    private $loadRandomBooths = false;

    function __construct() {
        $this->includeJQuery();
        $this->initialMeta();
        $this->meta("<script type = 'text/javascript' src = '".base()."/common/navigation-scripts.js?version=0.1'></script>");
    }

    function body($html) {
        $this->body = $html;
    }

    function firstSideBar($html, $title, $startCollapsed=true, $headerLink=null) {
        $this->sidebarFirst = $this->makeSidebarHTML($html, $title, $startCollapsed, $headerLink);
    }

    function lastSideBar($html, $title, $startCollapsed=true, $headerLink=null) {
        $this->sidebarLast = $this->makeSidebarHTML($html, $title, $startCollapsed, $headerLink);
    }

    function meta($metahtml) {
        $this->metaHTML = $this->metaHTML . "
        " . $metahtml;
    }

    function enableRandomBooths() {
        $this->loadRandomBooths = true;
    }

    function echoHtml() {

        session_start();
        $this->setErrorReporting();
        $link = connect_to_boothsite();
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
            echo "Please log in";
        }

        echo
"<!DOCTYPE html>
<html>
    <head>
        " . $this->metaData() . "
    </head>
    <body>"
        .$this->headerContents()."
        <div class = \"page_frame\">"
            ."<div class = \"body_inside\">
                ".$this->body."
            </div>
            <div class = \"sidebar_first\">
                ".$this->sidebarFirst."
            </div>
            <div class = \"sidebar_last\">
                ".$this->sidebarLast."
            </div>"
            .$this->footer()."
        </div>
    </body>
</html>";
    }

    private function includeJQuery()
    {
        $this->metaHTML .= "
        <script src=\"http://code.jquery.com/jquery-2.1.1.js\"></script>
        <script src=\"http://code.jquery.com/jquery-migrate-1.2.1.js\"></script>";
    }

    function metaData()
    {
        return $this->metaHTML;
    }

    private function setErrorReporting()
    {
        if (isset($_SESSION['username']) && $_SESSION['username'] == "bradsk88") {
            error_reporting(E_ALL);
        } else {
            error_reporting(E_ALL);
        }
    }

    function headerContents() {
        $headerlink = "/info/news";
        if (isset($_SESSION['username'])) {
            $headerlink = "/activity";
        }

        $alwaysThere = "
        <div class = 'pageheader'>
            <a href = \"".base().$headerlink."\" id=\"homebutton\">
                <div class = \"headertitle\"></div>
            </a>";

        if (!isset($_SESSION['username'])) {
            return $alwaysThere."
        </div>
        ";
        }
        return $alwaysThere
        . "<div class = \"headernavbutton\" onclick = \"openSnapNewBooth();\""
        . " style = \"background-image: url(".base()."/media/newbooth.png);\"></div>
                    <a href = \"/search\">
                        <div class = \"headernavbutton advsearchbutton\" onclick = \"openAdvancedSearch();\""
        . " style = \"background-image: url(".base()."/media/search.png);\"></div>
                    </a>
                    <form method = \"GET\" action = \"".base()."/searchresults\">
                        <input type = \"text\" class = \"searchtextarea\" name = \"q\"/>
                        <div class = \"searchchoiceswrapper\">
                            <select class = \"searchchoices\" name = \"scope\">
                                <option value = \"user\">Users</option>
                                <option value = \"booth\">Booths</option>
                                <option value = \"booth_comment\">Comments</option>
                            </select>
                        </div>
                        <button type = \"submit\" class = \"searchbutton\">Go</button>
                        <div style = \"clear: both;\"></div>
                    </form>
                    <canvas id = 'headgear' class = 'headerbutton' onclick='openSettings()'>
                    </canvas>
                </div>
        ";
    }

    private function footer() {
        //TODO: Bring this back
        return "";
        return "
        <div class = 'subheader' id = 'bottomlinks'>
            <a href = '".base()."/info/news'><span class = 'subheadernavbutton'>News</span></a>
            <a href = '".base()."/info/rules'><span class = 'subheadernavbutton'>Site Rules</span></a>
            <a href = '".base()."/info/contact'><span class = 'subheadernavbutton'>Contact</span></a>
            <a href = '".base()."/info/reportform?type=bug'><span class = 'subheadernavbutton'>Report Bug</span></a>
            <a href = '".base()."/info/reportform?type=feat'><span class = 'subheadernavbutton'>Request Feature</span></a>
            <a href = '".base()."/info/mission'><span class = 'subheadernavbutton'>Mission Statement</span></a>
        </div>";
    }

    private function initialMeta()
    {
        $this->metaHTML = $this->metaHTML . "
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <meta http-equiv='content-type' content='text/html; charset=UTF-8' />
        <meta name=\"keywords\" content=\"dailybooth, social, photography, photo, socialnetworking, microblogging, community, web2.0, pictures, blog, photos\">
        <title>Boothi.ca - Take a picture every day and make friends</title>
        <link rel='stylesheet' href='".base()."/css/master.css' type='text/css' media='screen' />
        <link rel='stylesheet' href='".base()."/css/pageframe.css' type='text/css' media='screen' />
        <link rel=\"shortcut icon\" href=\"".base()."/favicon.ico\" type=\"image/x-icon\">
        <script type = \"text/javascript\" src = \"".base()."/framing/PageFrame-scripts.js.php\"></script>";
    }

    public function script($absoluteUrl)
    {
        $this->meta("<script type = \"text/javascript\" src = \"".$absoluteUrl."\"></script>");
    }

    public function css($absoluteUrl)
    {
        $this->meta("<link rel='stylesheet' href='/css/".$absoluteUrl."' type='text/css' media='screen' />");
    }

    function makeSidebarHTML($html, $title, $startCollapsed, $absoluteHeaderLink) {
        $sidebarBodyClass = "sidebar_body";
        if ($startCollapsed) {
            $sidebarBodyClass = "sidebar_body collapsed";
        }
        if ($absoluteHeaderLink == null) {
            $sidebarButtonHTML = "";
        } else {
            $sidebarButtonHTML = "<div class = \"sidebar_button>
                <a href = \"".$absoluteHeaderLink."\">go</a>
            </div>";
        }
        return
        "<div class = \"sidebar_titleandbutton\">
            <div class = \"sidebar_title\">
                ".$title."
            </div>
            ".$sidebarButtonHTML."
        </div>
        <div class = \"".$sidebarBodyClass."\">
            ".$html."
        </div>
        ";
    }

}
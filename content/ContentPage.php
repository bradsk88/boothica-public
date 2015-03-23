<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 6/4/14
 * Time: 10:11 PM
 *
 * ContentPage should be used as the base for almost all major sections on Boothi.ca.
 *
 * You can easily create a basic page by creating a new ContentPage object, calling $page->body("your body HTML"),
 * and finally calling $page->echoPage().
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("cookies");
require_common("utils");

error_reporting(0);
if ($_SESSION['username'] == 'bradsk88') {
    error_reporting(E_ERROR);
}

define(BASE, base());

class ContentPage
{

    private $veryFirstHTML;
    private $bodyHTML;
    private $metaHTML;
    private $populateCenter = "void";
    private $redirectToLogin = true;
    private $includeSideBars = true;

    function __construct($defaultPopulateCenterFunctionName)
    {
        $this->includeJQuery();
        $this->populateCenter = $defaultPopulateCenterFunctionName;
        $this->meta("<script type = 'text/javascript' src = '".BASE."/common/navigation-scripts.js?version=0.1'></script>");
        $this->meta("<script type = 'text/javascript' src = '".BASE."/messages/pm-scripts.js'></script>");
        $this->meta("<script type = 'text/javascript' src = '".BASE."/livefeed/livefeed-scripts.js'></script>");
        $this->meta("<script type = 'text/javascript' src = '".BASE."/newbooth/newbooth-scripts.js'></script>");
    }

    function veryFirst($html)
    {
        $this->veryFirstHTML = $html;
    }

    function body($bodyhtml)
    {
        $this->bodyHTML = str_replace("\n", "\n                ", $bodyhtml);
    }

    function meta($metahtml)
    {
        $this->metaHTML .= "
        " . $metahtml;
    }

    function bodyAsSpinner()
    {
        $this->bodyHTML = $this->spinnerDiv();
    }

    private function includeJQuery()
    {
        $this->metaHTML .= "
        <script src=\"http://code.jquery.com/jquery-2.1.1.min.js\"></script>
        <script src=\"http://code.jquery.com/jquery-migrate-1.2.1.min.js\"></script>
        <script src=\"".BASE."/common/jcanvas.min.js\"></script>
        <script language=\"text/javascript\" src = \"http://code.jquery.com/ui/1.10.0/jquery-ui.js\"></script>";
    }

    function __toString() {
        $bodyOut = '';
        session_start();
        $this->setErrorReporting();
        $link = connect_to_boothsite();
        if (!$link) {
            die ("<script type = 'text/javascript'>document.write('There was a problem connecting to the database.  Probably the server just went down :(<p>Please try again in a few minutes.');</script></head></html>");
        }

        //session not started, check for remembrance cookie
        if (!isset($_SESSION['username']) && isset($_COOKIE['userid'])) {
            if (cookie_set() == 0) {
                $bodyOut .=  "Reloading. (This site requires JavaScript)";
                $bodyOut .=  "<script>parent.window.location.reload(true);</script>";
                return $bodyOut;
            }
            $bodyOut .=  "Please log in";
        }

        $headerlink = "".BASE."/info/news";
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $headerlink = "".BASE."/activity";
        }

        $bodyOut .=  "<!DOCTYPE html>
<html>
    <head>
        " . $this->metaData() . "
    </head>
    <body>
        " . $this->veryFirstHTML . "
        <div class = 'pageheader'>
            <a href = \"". $headerlink . "\" id=\"homebutton\">
                <div class = \"headertitle\"></div>
            </a>
            ";
        if (isset($_SESSION['username'])) {

            $bodyOut .=
                "<div class = \"headernavbutton\" onclick = \"openSnapNewBooth();\""
                . " style = \"background-image: url(".BASE."/media/newbooth.png);\"></div>
                            <a href = \"/search\">
                                <div class = \"headernavbutton advsearchbutton\" onclick = \"openAdvancedSearch();\""
                . " style = \"background-image: url(".BASE."/media/search.png);\"></div>
                            </a>
                            <form method = \"GET\" action = \"".BASE."/searchresults\">
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
                ";
        } else {
            $bodyOut .=  "<a href = '".BASE."/registration'><div class = \"headernavbutton\">Register</div></a>
            &nbsp;&nbsp;&nbsp;&nbsp;<a href = '".BASE."/login'><div class = \"headernavbutton\">Login</div></a>";
        }
        $bodyOut .=
            "       </div>
                    <div class = 'main'>
                        <div class = 'allcontent'>
                        <div class = 'centerandright'>
                            <div class = 'centerpane' id = 'centerpane'>
                                " . $this->bodyHTML . "
                </div>
                <div class = 'rightpane' id = 'rightpane'>
";

        $usercard = "";

        if (isset($_SESSION['username']) && $this->includeSideBars) {
            $displayName = getDisplayName($username);
            $userImage = UserImage::getImage($username);

            $usercard =
                "<div class = 'usercardimage' onclick = \"openUserFeed('" . $username . "')\" style = 'background-image: url(".BASE. (string)$userImage . ")'></div>
                        <div class = \"usercardcontent\">
                            <div class = 'usercardname'>
                                <span onclick = \"openUserFeed('" . $username . "')\">@" . $displayName . "</span>
                            </div>
                            <div class = 'usercardstats'>
                                <a id = \"boothsnum\" href = '".BASE."/users/" . $username . "' onclick = \"openUserFeed('" . $username . "')\">
                                    ??? Booths
                                </a> / <a id = \"friendsnum\" href = '/users/" . $username . "/friends'>
                                    ??? Friends
                                </a>
                            </div>
                        </div>
                        <div style = 'clear:both;'></div>";
        }

        if ($this->includeSideBars) {

            if (isset($_SESSION['username']) && $this->includeSideBars) {
                $bodyOut .=
                    "                               <div class = 'usercard' id = 'usercardright'>
                                                        ".$usercard.
                    "                               </div>";
            }

            $bodyOut .=
                "                               <div class = 'usercard' id = 'rightcard'>
                                                    <div class = 'usercardimage' style = 'background-image: url(".BASE."/media/messages.png); background-repeat: no-repeat; background-size: auto;'></div>
                                    <div class = \"usercardcontent\">
                                        <div class = 'usercardtext' onclick=\"reloadUsersNotifications()\">Notifications</div>
                                        <div class = 'usercardnumwrap' id = 'notifscountwrap'>
                                            <div class = 'usercardnum' id = 'notifscount'></div>
                                        </div>
                                        <div style = 'clear:both;'></div>
                                        <div class = 'usercardtext' onclick=\"reloadUsersPMs()\">Private Messages</div>
                                        <div class = 'usercardnumwrap' id = 'pmscountwrap'>
                                            <div class = 'usercardnum' id = 'pmscount'></div>
                                        </div>
                                        <div style = 'clear:both;'></div>
                                    </div>
                                    <div style = 'clear:both;'></div>
                                </div>
                                <div id= 'rightfeed'>
                                </div>
                                <div id= 'primaryfeedright'>
                                </div>
            ";
        }
        $bodyOut .=
            "               </div>
                            <div style = 'clear:both;'></div>
                        </div>
                                        <div class = 'leftpane' id = 'leftpane'>";
        if (isset($_SESSION['username']) && $this->includeSideBars) {
            $bodyOut .=  "
                <div class = 'usercard' id = 'leftcard'>" .
                $usercard."
                </div>";
        }
        if ($this->includeSideBars) {
            $bodyOut .=
                "                <div id= 'leftfeed'>
                                </div>";
        }
        $bodyOut .=
            "               </div>
                        </div>
                    </div>
                    <div style = \"clear:both\"></div>
                    <div class = 'subheader'>
                        <a href = '".BASE."/info/news'><span class = 'subheadernavbutton'>News</span></a>
                    <a href = '".BASE."/info/rules'><span class = 'subheadernavbutton'>Site Rules</span></a>
                    <!--TODO<a href = '".BASE."/info/tos'><span class = 'subheadernavbutton'>Terms of Service</span></a>-->
                    <a href = '".BASE."/info/contact'><span class = 'subheadernavbutton'>Contact</span></a>
                    <a href = '".BASE."/info/reportform?type=bug'><span class = 'subheadernavbutton'>Report Bug</span></a>
                    <a href = '".BASE."/info/reportform?type=feat'><span class = 'subheadernavbutton'>Request Feature</span></a>
                    <a href = '".BASE."/info/mission'><span class = 'subheadernavbutton'>Mission Statement</span></a>
                </div>
                </body>
            </html>";
        return $bodyOut;
    }

    function echoPage()
    {
        echo (string)$this;
    }

    function metaData()
    {
        $str = "<meta http-equiv='content-type' content='text/html; charset=UTF-8' />
        <meta name=\"keywords\" content=\"dailybooth, social, photography, photo, socialnetworking, microblogging, community, web2.0, pictures, blog, photos\">
		<title>Boothi.ca - Take a picture every day and make friends</title>
		<link rel='stylesheet' href='".BASE."/css/master.css' type='text/css' media='screen' />
		<link rel='stylesheet' href='".BASE."/css/contentpage.css' type='text/css' media='screen' />
		<link rel=\"shortcut icon\" href=\"".BASE."/favicon.ico\" type=\"image/x-icon\">
		<script type = \"text/javascript\">defaultPopulateCenterFunction = \"" . $this->populateCenter . "\";</script>";
        if ($this->redirectToLogin) {
            $str .= "<script type = \"text/javascript\" src = \"".BASE."/content/ContentPage-scripts.js\"></script>";
        }
        $str = $this->metaHTML . $str;
        return $str;
    }

    private function setErrorReporting()
    {
        if (isset($_SESSION['username']) && $_SESSION['username'] == "bradsk88") {
            error_reporting(E_ALL);
        } else {
            error_reporting(E_ALL);
        }
    }

    function spinnerDiv()
    {
        return "<div class = \"loadspinner\"></div>";
    }

    public function doNotRedirectToLogin()
    {
        $this->redirectToLogin = false;
    }

    public function excludeSideBars()
    {
        $this->includeSideBars = false;
    }

}

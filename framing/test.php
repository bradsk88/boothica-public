<?php
/**
 * Created by PhpStorm.
 * User: bradsk88
 * Date: 12/26/14
 * Time: 3:49 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";

main();

function main() {

$page = new PageFrame();

$body = <<<EOT
<div class = "welcome-section">
    <div class = "welcome-logo">
        Welcome to Boothi.ca
    </div>
    <div class = "welcome-login">
        <div class = "welcome-login-header">
            Existing users:
        </div>
        <form action = '/dologin' method=post>
            <div class = "welcome-username-label">
                Username
            </div>
            <div class = "welcome-username-field">
                <input type = 'text' name = 'username'/>
            </div>
            <div class = "welcome-username-end"></div>
            <div class = "welcome-password-label">
                Password
            </div>
            <div class = "welcome-password-field">
                <input type = 'password' name = 'password'/>
            </div>
            <div class = "welcome-login-button">
                <button type = 'submit'>Log in</button>
            </div>
        </form>
    </div>
    <div class = "welcome-brief">
        <div class = "welcome-brief-text">Boothi.ca is a place to share your face and your thoughts with the internet</div>
        <div class = "welcome-signup-button">
            <button>Click here to sign up</button>
        </div>
    </div>
</div>
EOT;

$page->body($body);
$page->firstSideBar(makeRandomBoothsSideBar(), "Random Booths", false);
$page->lastSideBar(makePublicFeedSideBar(), "New Public Booths");
$page->css("welcome.css");
$page->meta("<link href='http://fonts.googleapis.com/css?family=Bitter:400,700' rel='stylesheet' type='text/css'>");

$page->echoHtml();

}

function makeRandomBoothsSideBar() {
    //TODO: This
    return "<div style = \"background-color: #F00; width: 100%; height: 30em;\">
    Random Booths
    </div>";
}

function makePublicFeedSideBar() {
    //TODO: This
    return "<div style = \"background-color: #0F0; width: 100%; height: 30em;\">
    PublicFeed
    </div>";
}
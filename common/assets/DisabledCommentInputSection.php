<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/13/13
 * Time: 9:51 PM
 * To change this template use File | Settings | File Templates.
 */

class DisabledCommentInputSection {

    public static function suspended() {
        $msg = "You are suspended for violation of the user agreement\n\n
        If you believe this is a mistake and would like your account reopened,
        please contact support@boothi.ca";
        return new DisabledCommentInputSection($msg);
    }

    public static function banned() {
        $msg = "You are banned for violation of the user agreement";
        return new DisabledCommentInputSection($msg);
    }
    public static function notLoggedIn() {
        $msg = "Sign in to read and add comments";
        return new DisabledCommentInputSection($msg);
    }

    public static function error() {
        $msg = "Unexpected Error";
        return new DisabledCommentInputSection($msg);
    }
    private $str;

    function __construct($msg) {
        $this->str = "
						<form>
								<textarea id = 'commentarea' style='width: 800px; height: 100px; resize: none;' name='comment' disabled='disabled'>".$msg."</textarea>
						</form>
				";
    }

    function __toString() {
        return $this->str;
    }

}
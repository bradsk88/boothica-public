<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/8/13
 * Time: 11:19 PM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/UserImage.php";

/**
 * Class UserIcon
 * Requires a connection to boothsite database.
 * Requires open session to show private icons.
 */
class UserIcon {

    private $img = "/media/error.png";
    private $string;

    public function __construct($username, $string) {
        $this->string = $string;
    }

    public static function standardIcon($username) {
        return new UserIcon($username, UserIcon::makeDiv($username, "commenticon"));
    }

    public static function friendIcon($username) {
        return UserIcon::from($username, "friendicon");
    }

    public static function from($username, $divtype) {
        return new UserIcon($username, UserIcon::makeDiv($username, $divtype));
    }

    private static function makeDiv($username, $divclass) {
        $img = new UserImage($username);
        return "<div class = '".$divclass."' style='background-image: url(".$img.");'></div>";
    }

    public function __toString() {
        return $this->string;
    }

}
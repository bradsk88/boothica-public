<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/13/13
 * Time: 10:36 PM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_asset("AbstractCommentInputSection");
require_asset("DisabledCommentInputSection");
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/AbstractCommentInputSection.php";


class NewsCommentInputSection extends AbstractCommentInputSection {

    public static function newInstance($number, $boother) {
        checkNotNull($number);
        checkNotNull($boother);
        if (!isset($_SESSION['username'])) {
            return DisabledCommentInputSection::notLoggedIn();
        } else if (isBanned($_SESSION['username'])) {
            return DisabledCommentInputSection::banned();
        } else if (isSuspended($_SESSION['username'])) {
            return DisabledCommentInputSection::suspended();
        } else {
            return new NewsCommentInputSection($number, $boother);
        }
    }

    public function __construct($number, $boother) {
        parent::__construct($number, $boother);
        parent::disableImageComments();
    }

    protected function commentJsFile() {
        return "/news_comments.js";
    }

    protected function formAction() {
        return "/actions/news_comment/upload.php";
    }

}
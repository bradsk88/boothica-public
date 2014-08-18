<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/12/13
 * Time: 7:20 PM
 * To change this template use File | Settings | File Templates.
 */

use comment\CommentObj;

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/DateStamp.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");

class CommentDateStamp extends DateStamp {

    /**
     * @var $comment CommentObj
     */
    public function __construct($comment) {
        parent::__construct($comment->getDateTime(), $comment->getHours(), $comment->getMinutes(), "+00:00");
    }

}
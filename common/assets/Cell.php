<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/8/13
 * Time: 10:56 PM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_asset("DateStamp");

abstract class Cell {

    private $dateString;

    protected function __construct($date) {
        $this->dateString = DateStamp::forDateTime($date, "+00:00");
    }

    protected function getDateString() {
        return $this->dateString;
    }

}
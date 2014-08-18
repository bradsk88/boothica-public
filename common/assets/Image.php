<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/11/13
 * Time: 1:59 PM
 * To change this template use File | Settings | File Templates.
 */

class Image {

    private $name;
    private $type;

    public function __construct($name, $type) {
        $this->type = $type;
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

}
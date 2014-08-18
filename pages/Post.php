<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/8/13
 * Time: 10:35 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class Post {

    function show() {
      include "{$_SERVER['DOCUMENT_ROOT']}/content/html.php";
      $this->headScripts();
      include "{$_SERVER['DOCUMENT_ROOT']}/content/top.php";
      $this->preScripts();
      $this->doShow();
      include "{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php";
    }

    protected abstract function headScripts();

    protected abstract function preScripts();

    protected abstract function doShow();

}
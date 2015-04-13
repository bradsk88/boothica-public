<?php

class BetaBalance {

    private $amount;

    function __construct($username) {

        $sql = "SELECT `beta` FROM `currencytbl` WHERE `fkUsername` = '".$username."' LIMIT 1;";
        $result = mysql_query($sql);
        if (!$result) {
            mysql_death1($sql);
            $this->amount = -1;
        }
        $rows =  mysql_fetch_array($result);
        $this->amount = $rows['beta'];

    }

    function __toString() {
        if ($this->amount == -1) {
            return "???";
        }
        return $this->amount;
    }

}
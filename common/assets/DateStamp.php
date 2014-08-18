<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/12/13
 * Time: 5:54 PM
 * To change this template use File | Settings | File Templates.
 */

class DateStamp {

    /** @const */
    private static $MONTHS = array('Durfembarary', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novemeber', 'December');

    private $hours;
    private $minutes;
    private $dateTime;

    public static function forDateTime($dateTime, $offset) {
        //Use 25 hours so it will always use the date, rather than hours/mins.
        return new DateStamp($dateTime, 25, 0, $offset);
    }

    public static function forDateTimeUTC($dateTime) {
        //Use 25 hours so it will always use the date, rather than hours/mins.
        return new DateStamp($dateTime, 25, 0, "+00:00");
    }

    function __construct($dateTime, $hours, $minutes, $offset) {
        $this->hours = $hours;
        $this->minutes = $minutes;
        $this->dateTime = $dateTime;
        $this->offset = $offset;
    }

    function __toString() {
        if ($this->hours > 24) {
            return $this->oldDate();
        } else {
            return $this->todayDate();
        }
    }

    public function oldDate()
    {
        $exploded = explode(':', $this->offset);
        $hour_off = $exploded[0];
        $min_off = $exploded[1];
        $dateTime = $this->dateTime;
        $dateTime = explode(' ', $dateTime);
        $time = $this->formatTime($dateTime[1], $hour_off, $min_off);
        $dateTime = explode('-', $dateTime[0]);
        $year = $dateTime[0];
        $month = ltrim($dateTime[1], "0");
        $day = $dateTime[2];
        $dateString = $time." - ".DateStamp::$MONTHS[$month]." ".$day.", ".$year;

        return "
				<div class = 'feeddate'>
					".$dateString."
				</div>";
    }

    private function formatTime($time, $hour_off, $min_off) {
        $timeEx = explode(':', $time);
        $hour = $timeEx[0];
        $min = $timeEx[1];
        $sec = $timeEx[2];
        $hour = $hour + $hour_off;
        $min = $min + $min_off;
        $trimmedhour = $hour;
        if ($hour > 0) {
            $trimmedhour = ltrim($hour, '0');
        }
        return $trimmedhour .":".str_pad($min, 2, '0', STR_PAD_LEFT).":".$sec;
    }

    public function todayDate()
    {
        $unit = "hours";
        if (1 == $this->hours) {
            $unit = "hour";
        }
        return "
				<div class = 'feeddate'>
					" . $this->hours . " " . $unit . ", " . $this->minutes . " minutes ago.
				</div>";
    }

}
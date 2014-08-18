<?php

    require_once "{$_SERVER['DOCUMENT_ROOT']}/donations/DonationInfo.php";

    class DonationBar {

        const WIDTH = 400;
        const GOAL = 127.67;

        function show() {
            $this->showWithOverlay("", true, self::WIDTH);
        }

        function showWithOverlay($text) {
            $this->doShow($text, false, self::WIDTH);
        }

        function showWithWidth($width) {
            $this->doShow("",false , $width);
        }

        function showWithOverlayAtWidth($text, $width) {
            $this->doShow($text, false, $width);
        }

        private function doShow($text, $showDescription,$width) {
            //TODO: Create a more general progress bar superclass for other purposes.

            $donationInfo = new DonationInfo();

            if ($donationInfo->loadSucceeded()) {
                $goal = self::GOAL;
                $raised = $donationInfo->getRaisedDollars();
                $percent = $donationInfo->getPercentRaisedOf($goal);
                if ($text === "") {
                    if ($percent > 75) {
                        $text = "Only $".($goal-$raised)." left until Boothi.ca is safe for another year.  Please click here to donate!";
                    } else {
                        $text = $percent ."% of donation goal recieved.  Please Donate";
                    }
                }
//                $fillWidth = ($width * $percent) / 100;
//                <a href = \"/info/news\">
//				    <div class = 'headersocialmessage'>
//                        ".getSocialNotice()."
//                    </div>
//                </a>
                echo "
                    <a href = '/info/donations'>
                    <div class = \"headersocialmessage\">
                        ".$text."
                    </div>
                    </a>
                ";
                if ($showDescription) {
                    echo "Raised so far: $".$raised."CAD<br/>Goal: $".$goal."CAD";
                }
                return;
            }
            echo " <div align = left class = \"progressbarbackground\"  style = \"position: relative; width: ".$width."px; height: 20px;\">There was a problem loading our donation stats.</div>";
        }

        function showWithOverlayAndDescription($text) {
            $this->doShow($text, true, self::WIDTH);
        }

    }

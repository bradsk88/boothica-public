<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/12/13
 * Time: 5:48 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class AbstractCommentInputSection {
    private $number;
    private $boother;
    private $commenttarget = "comment_target";
    private $allowImages = true;
    private $includeBaseJS = true;

    protected function disableImageComments() {
        $this->allowImages = false;
    }

    protected function sendToNewTab() {
        $this->commenttarget = "_blank";
    }

    public function __construct($number, $boother) {
        $this->number = $number;
        $this->boother = $boother;
    }

    public function __toString() {
        
        $str = "
                <script type = \"text/javascript\" src = \"/newbooth/jquery.FileReader.js\"></script>
                <form id = \"comment_form\" action=\"".$this->formAction()."\" method=\"post\" target = \"".$this->commenttarget."\">
                    <div class = \"commentinputsection\">
                        <input type = \"hidden\" name = \"number\" value = \"".$this->number."\"/>
						<input type = \"hidden\" name = \"boother\" value = \"".$this->boother."\"/>
						<input type = \"hidden\" id = \"image\" name = \"image\" />
                        <textarea id = \"commentarea\" class = \"commenttextarea\" name=\"comment\"></textarea>
        ";
        if ($this->allowImages) {
            $str .= "
                        <div class = \"photowrapper\">
                            <div class = \"photocommentsection\" id = \"photocommentsection\"></div>
                        </div>
                        <div style = \"clear: both\"></div>
                        <div class = \"commentaddpic\">
                            <div class = \"webcambuttons\">
                                <div class = \"webcambutton\" id = \"camsnapbutton\">Snap!</div>
                                <div class = \"webcambutton\" id = \"camcountdownbutton\">Count...</div>
                                <div class = \"webcambutton\" id = \"camtofilebutton\">File</div>
                                <div class = \"piccancelbutton\">Cancel</div>
                                <div style = \"clear: both;\"></div>
                            </div>
                            <div class = \"filebuttons\" style = \"display: inherit;\">
                                <div style = \"display: none;\">
                                    <input type = \"file\" id = \"fileinput\" onChange = \"showPreview(this.files)\"/>
                                </div>
                                <div class = \"webcambutton\" id = \"filebrowsebutton\">Browse...</div>
                                <div class = \"webcambuttonspace\"></div>
                                <div class = \"webcambutton\" id = \"filetocambutton\">Webcam</div>
                                <div class = \"piccancelbutton\">Cancel</div>
                                <div style = \"clear: both;\"></div>
                            </div>
                            <div class = 'countdown'></div>
                            <div class = \"camresetbutton\">Reset</div>
                            <div class = \"commentaddpicfwbutton\">Add Picture</div>
                        </div>
            ";
        }
        $str .= "
                        <div class = \"commentpostbuttonwrapper\">
                            <button class = \"commentpostbutton\" type = \"submit\">Post Comment</button>
                        </div>
                        <div style = \"clear: both\"></div>
                    </div>
                </form>
        ";
        if ($this->includeBaseJS) {
            $str .=
"              <script type ='text/javascript' src='/comment/base.js'></script>
        ";
        }
        $str .=
                "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js\"></script>
                <script type ='text/javascript' src='/common/preview_scripts.js'></script>
                <script type ='text/javascript' src='".$this->commentJsFile()."'></script>
        ";
        return $str;
    }

    protected function includeBaseJS($include) {
        $this->includeBaseJS = $include;
    }
    protected abstract function commentJsFile();
    protected abstract function formAction();
}
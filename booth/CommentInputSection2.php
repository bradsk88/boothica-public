<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_asset("AbstractCommentInputSection");

class CommentInputSection2 extends AbstractCommentInputSection {

    public function __construct($number, $boother) {
        parent::__construct($number, $boother);
        parent::includeBaseJS(false);
    }

    protected function commentJsFile() {
        if (isDeveloper($_SESSION['username'])) {
            return "/comment/input.js";
        }
        return "/comment/input.js";
    }

    protected function baseJsFile() {
        return "/activity/commentposting.js";
    }

    protected function formAction() {
        return "/actions/comment/upload.php";
    }

} 
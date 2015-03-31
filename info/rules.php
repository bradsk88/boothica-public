<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";

$page = new ContactPage();
echo $page->render();

class ContactPage {

    private $delegate;

    function __construct() {
        $this->delegate = new PageFrame();
        $this->delegate->useDefaultSideBars();
        $this->delegate->excludeLoginNotification();
        $this->delegate->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/info/templates/rules.mst", array());
    }

    function render() {
        return $this->delegate->render();
    }

}
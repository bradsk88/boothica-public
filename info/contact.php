<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";

$page = new ContactPage();
echo $page->render();

class ContactPage {

    private $delegate;

    function __construct() {
        $this->delegate = new PageFrame();
        $this->delegate->useDefaultSideBars();
        $this->delegate->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/info/templates/contact.mst", array());
    }

    function render() {
        $del = $this->delegate;
        $del->css(base()."/css/contact.css");
        return $del->render();
    }

}
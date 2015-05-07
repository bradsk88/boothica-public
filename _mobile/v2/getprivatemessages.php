<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
require_lib("h2o-php/h2o");
require_common("db");
require_asset("UserImage");

class GetPrivateMessagesResponse extends AbstractUserApiResponse {

    function __construct() {
        parent::__construct(array("otherUsername"));
    }

    protected function run($username)
    {

        if (!userExists($_POST['otherUsername'])) {
            $this->markCallAsFailure("User ".$_POST['otherUsername']." does not exist");
            return;
        }

        $pagenum = 1;
        if (isset($_REQUEST['pagenum'])) {
            $pagenum = $_REQUEST['pagenum'];
        }

        $numperpage = 10;
        if (isset($_REQUEST['numperpage'])) {
            $numperpage = $_REQUEST['numperpage'];
        }

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/getUserPMs.mst.sql");
        $sql = $sqlBuilder->render(array(
            "username" => $username,
            "otherUsername" => $_POST['otherUsername']
        ));

        $dblink = connect_boothDB();
        $query = $dblink->query($sql);
        if (!$query) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }

        $out = array();
        while ($row = $query->fetch_array()) {
            $out[] = array(
                "otherUsername" => $row['username'],
                "otherUserDisplayName" => (string) getDisplayName($row['username']),
                "text" => decryptPrivateMessage($row['message'])
            );
        }

        if (isset($_REQUEST['markread']) && $_REQUEST['markread']) {
            $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/markUsersPMsRead.mst.sql");
            $sql = $sqlBuilder->render(array(
                "username" => $username,
                "otherUsername" => $_POST['otherUsername']
            ));
            $query = $dblink->query($sql);
            if (!$query) {
                $this->markCallAsFailure(sql_death1($sql));
                return;
            }
        }

        $this->markCallAsSuccessful("User PM Conversation get OK", array("messages" => $out));
    }
}

$response = new GetPrivateMessagesResponse();
$response->runAndEcho();

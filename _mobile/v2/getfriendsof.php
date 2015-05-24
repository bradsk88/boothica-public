<?PHP

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("db");
require_common("utils");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");
require_lib("h2o-php/h2o");
require_asset("UserImage");

class FriendListActivity extends AbstractUserApiResponse {

    function __construct() {
        parent::__construct(array("boothername"));
    }

    protected function run($username)
    {
        $boothername = $_REQUEST['boothername'];

        if (doesUserAppearPrivate($boothername)) {
            $this->markCallAsFailure(getDisplayName($username)." is not allowed to view ".getPossessiveDisplayName($boothername)." friends.");
            return;
        }

        $pagenum = 1;
        if (isset($_REQUEST['pagenum'])) {
            $pagenum = $_REQUEST['pagenum'];
        }

        $limitsGiven = false;
        $numperpage = 10;
        if (isset($_REQUEST['numperpage'])) {
            $limitsGiven = true;
            $numperpage = $_REQUEST['numperpage'];
        }

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/friendList.mst.sql");
        $dblink = connect_boothDB();

        $values = array(
            "bootherName" => $dblink->real_escape_string($boothername),
            "limitsGiven" => $limitsGiven
        );

        if ($limitsGiven) {
            $values['startIndex'] = ($pagenum-1) * $numperpage;
            $values['numPerPage'] = $dblink->real_escape_string($numperpage);
        }

        $sql = $sqlBuilder->render($values);

        $query = $dblink->query($sql);
        $boothers = array();
        while ($row = $query->fetch_array()) {
            $boothers[] = array(
                "bootherImageUrl" => UserImage::getAbsoluteImage($row['username']),
                "bootherDisplayName" => (string) getDisplayName($row['username']),
                "bootherName" => $row['username']
            );
        }

        $this->markCallAsSuccessful("Friends get OK", array("boothers" => $boothers));
    }
}

$activity = new FriendListActivity();
$activity->runAndEcho();

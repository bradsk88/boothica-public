<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");
require_lib("h2o-php/h2o");
require_common("db");
require_asset("UserImage");

class LikeUsersApiResponse extends AbstractUserApiResponse {

    function __construct() {
        parent::__construct(array("boothnum"));
    }

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {
        $sqlBuiler = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/getLikeUsers.mst.sql");
        $sql = $sqlBuiler->render(array(
            'boothNumber' => $_REQUEST['boothnum']
        ));
        $dblink = connect_boothDB();
        $result = $dblink->query($sql);
        if (!$result) {
            echo json_encode(array("error" => sql_death1($sql)));
            return;
        }
        $out = array();
        while ($row = $result->fetch_array()) {
            $out[] = array(
                "userImageAbsoluteUrl" => UserImage::getAbsoluteImage($row['fkUsername']),
                "username" => $row['fkUsername']
            );
        }
        $this->markCallAsSuccessful("Get booth likes OK", array(
            "likeusers" => $out
        ));
    }
}

$response = new LikeUsersApiResponse();
$response->runAndEcho();

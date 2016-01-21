<?PHP

    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
    require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
    require_lib("h2o-php/h2o");
    require_common("db");

class ClearAllMentionsResponse extends AbstractUserApiResponse {

    protected function run($username)
    {

        if ($this->fail_if_the_end()) {
            return;
        }

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/clearAllMentions.mst.sql");
        $sql = $sqlBuilder->render(array("username" => $username));
        $dblink = connect_boothDB();
        $query = $dblink->query($sql);
        if (!$query) {
            $this->markCallAsFailure(sql_death1($sql));
        }

        $this->markCallAsSuccessful("Marked all mentions as read");

    }
}

$response = new ClearAllMentionsResponse();
$response->runAndEcho();

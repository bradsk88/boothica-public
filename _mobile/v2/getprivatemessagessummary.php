<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
require_lib("h2o-php/h2o");
require_common("db");
require_asset("UserImage");

class GetPrivateMessagesResponse extends AbstractUserApiResponse {

    protected function run($username)
    {
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

        $dblink = connect_boothDB();
        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/getPMSummary.mst.sql");
        $values = array(
            "username" => $dblink->real_escape_string($username),
            "limitsGiven" => $limitsGiven
        );
        if ($limitsGiven) {
            $values['startIndex'] = ($pagenum-1) * $numperpage;
            $values['numPerPage'] = $dblink->real_escape_string($numperpage);
        }
        $sql = $sqlBuilder->render($values);

        $query = $dblink->query($sql);
        if (!$query) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }

        $booths = array();
        while ($row = $query->fetch_array()) {
            $fromusername = $row['name'];

            $sql = "SELECT COUNT(*) as `num`
                FROM `privatemsgtbl`
                WHERE `toUsername` = '".$username."'
                AND `fromUsername` = '".$fromusername."'
                AND `isRead` = '0'
                LIMIT 10";
            $result3 = sql_query($sql);

            $hasnew = false;
            if (!$result3) {
                $this->markCallAsFailure(sql_death1($sql));
                return;
            }

            $r = $result3->fetch_array();
            $num = $r['num'];
            if ($num > 0) {
                $hasnew = true;
            }
            if ($num == 10) {
                $num = "9+";
            }

            $booths[] = array(
                'username' => $fromusername,
                'userdisplayname' => (string)getDisplayName($fromusername),
                'absoluteIconImageUrl' => (string) UserImage::getAbsoluteImage($fromusername),
                'hasnew' => $hasnew,
                'num' => $num
            );
        }
        $this->markCallAsSuccessful("User PM Conversation get OK", array("users" => $booths));
    }
}

$response = new GetPrivateMessagesResponse();
$response->runAndEcho();

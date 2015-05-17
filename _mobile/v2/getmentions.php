<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
require_lib("h2o-php/h2o");
require_common("utils");
require_common("db_auth");
require_asset("UserImage");

class AddFriendResponse extends AbstractUserApiResponse {

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {
        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/getMentions.mst.sql");
        $dblink = connect_boothDB();
        $sql = $sqlBuilder->render(array(
            "username" => $dblink->real_escape_string($username)
        ));
        $query = $dblink->query($sql);
        if (!$query) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }

        $out = array();
        $newMentionsExist = false;
        while ($row = $query->fetch_array()) {
            $out[] = array(
                "text" => $row['text'],
                "mentionerUsername" => $row['mentioner'],
                "mentionerDisplayname" => (string) getDisplayName($row['mentioner']),
                "mentionerPosessiveDisplayname" => (string) getPossessiveDisplayName($row['boother']),
                "mentionerIconAbsoluteImageUrl" => UserImage::getAbsoluteImage($row['mentioner']),
                "bootherUsername" => $row['boother'],
                "bootherDisplayname" => (string) getDisplayName($row['boother']),
                "bootherPosessiveDisplayname" => (string) getPossessiveDisplayName($row['boother']),
                "boothNumber" => $row['boothnumber'],
                "boothAbsoluteImageUrl" => base()."/booths/".$row['imageTitle'].".".$row['filetype'],
                "boothIconAbsoluteImageUrl" => base()."/booths/tiny/".$row['imageTitle'].".".$row['filetype']
            );
            $newMentionsExist = $newMentionsExist || $row['isNew'];
        }
        $this->markCallAsSuccessful("Mentions get OK", array(
            "mentions" => $out,
            "contains_new" => $newMentionsExist
        ));
    }
}

$response = new AddFriendResponse();
$response->runAndEcho();

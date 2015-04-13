<?PHP

session_start();
if (isset($_SESSION['username']) && isset($_POST['zone'])) {

    $username = $_SESSION['username'];

    include("{$_SERVER['DOCUMENT_ROOT']}/common/utils.php");
    $link = connect_to_boothsite();

    $zone = $_POST['zone'];
    $sql = "UPDATE
            `logintbl`
            SET `zone` = '".$zone."'
            WHERE `username` = '".$username."';";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
        return -1;
    }
    return 0;

} else {

	include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
	include("{$_SERVER['DOCUMENT_ROOT']}/content/top.php");
	include("{$_SERVER['DOCUMENT_ROOT']}/errors/404.php");
	include("{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php");

}

<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_page("ErrorPage");
require_page("InfoPage");
require_page("LoginPage");

if (!isLoggedIn()) {
    $page = new LoginPage();
    echo $page->render();
    return;
}

if (!isset($_REQUEST['friendname'])) {
    $page = new ErrorPage("Missing parameter: friendname");
    $page->echoHtml();
    return;
}

if (!(isset($_REQUEST['confirm']) && $_REQUEST['confirm'])) {
    $page = new InfoPage("Remove Friend", "Are you sure you want to un-friend ".$_REQUEST['friendname'], base()."/users/".$_SESSION['username']."/friends/remove/".$_REQUEST['friendname']."/confirm", "Confirm");
    $page->echoHtml();
    return;
}

$strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
session_write_close();

$url = base()."/_mobile/v2/removefriend.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
$friendName = $_REQUEST['friendname'];
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'boothername' => $friendName
)));
$result = curl_exec($ch);

if(get_magic_quotes_gpc()){
    $d = stripslashes($result);
}else{
    $d = $result;
}
$result = json_decode($d,true);

curl_close($ch);

if (isset($result['success'])) {
    if (isset($_REQUEST['nextUrl'])) {
        header("Location: ".$_REQUEST['nextUrl']);
        return;
    }
    header("Location: ".base()."/users/". $result['apiUsername']."/friends");
    return;
}

$page = new ErrorPage($result['error'], $_REQUEST['nextUrl']);
$page->echoHtml();

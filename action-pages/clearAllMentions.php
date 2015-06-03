<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_page("ErrorPage");
require_page("LoginPage");

if (!isLoggedIn()) {
    $page = new LoginPage();
    echo $page->render();
    return;
}

$strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
session_write_close();

$url = base()."/_mobile/v2/clearallmentions.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
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
    header("Location: ".base()."/mentions");
    return;
}

$page = new ErrorPage($result['error'], $_REQUEST['nextUrl']);
$page->echoHtml();

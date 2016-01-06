<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

error_reporting(E_ALL);

session_start();

if (!isLoggedIn()) {
    echo "ERROR";
    return;
}

$username = $_SESSION['username'];

$hash = hash('sha256', $username);
echo $hash."<br/>";

$strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';

session_write_close();

$url = base()."/_mobile/v2/userfeed.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'boothername' => $username
)));
$result = curl_exec($ch);

if(get_magic_quotes_gpc()){
    $d = stripslashes($result);
}else{
    $d = $result;
}
$result = json_decode($d,true);

curl_close($ch);

if (!$result['success']) {
    echo "ERROR";
    return;
}

foreach ($result['success']['booths'] as $booth) {
    echo "BOOTH";
}

echo "end";
<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_lib('h2o-php/h2o');

error_reporting(E_ALL);

session_start();

if (!isLoggedIn()) {
    echo "ERROR";
    return;
}

$username = $_SESSION['username'];
$num = rand(0, 99999);
$hash = hash('sha256', $num.''.$username);

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

$relative_dir = sprintf("/the_end/%s/%s-%s", date("Y-m-d"), date("H:i:s"), $hash);

$absolute_dir = sprintf("{$_SERVER['DOCUMENT_ROOT']}%s", $relative_dir);

if (!mkdir($absolute_dir, 0755, true)) {
    echo "Critical error";
    return;
}

$next_page_num = 0;
# TODO: previous page num

foreach ($result['success']['booths'] as $booth) {
    try {
        $page = build_css_block('pageframe');
        $page .= build_css_block('oneBooth-page');
        $page .= build_css_block('booth');
        $page .= build_css_block('posts');
        $page .= "<style>
    #booth_buttons {
        visibility: visible !important;
    }
</style>";
        $page .= build_standalone_page($booth, $next_page_num, 0);
        $next_page_num = $booth['boothnum'];

        $out_file = sprintf("%s/%s.html", $absolute_dir, $booth['boothnum']);
        file_put_contents($out_file, $page);
        syslog(LOG_INFO, "Wrote booth file to " . $out_file);
    } catch (Exception $e) {
        echo "There was a problem";
        death($e->getMessage());
        break;
    }
}

zip_and_serve($username, $absolute_dir, $relative_dir);

function build_css_block($file_name) {
    $css_as_string = file_get_contents(sprintf("{$_SERVER['DOCUMENT_ROOT']}/css/%s.css", $file_name));
    return sprintf("<style>%s</style>", $css_as_string);
}

function build_standalone_page($booth, $next_page_num, $prev_page_num) {
    $pageFrame = new PageFrame();

    $filetype = $booth['filetype'];
    $image_file = sprintf("{$_SERVER['DOCUMENT_ROOT']}/booths/%s.%s", $booth['imageHash'], $filetype);
    $im = file_get_contents($image_file);
    $imdata = base64_encode($im);
    $image_src = sprintf('data:image/%s;base64,%s', $filetype, $imdata);
    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/booth/templates/oneBooth.mst");
    $boothBody = $htmlBuilder->render(array(
        'blurb' => $booth['blurb'],
        'boothImageUrl' => $image_src
    ));

    $prevBoothUrl = sprintf("%s.html", $prev_page_num);
    $nextBoothUrl = sprintf("%s.html", $next_page_num);

    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/user-pages/templates/oneBoothFrame.mst");
    $commentsBody = "Comments!";

    $html = $htmlBuilder->render(array(
        "baseUrl" => '',
        "allowed" => True,
        "isOwner" => False,
        "commentInput" => "",
        "commentsBody" => $commentsBody,
        "boothBody" => $boothBody,
        "username" => $booth['username'],
        "boothNumber" => $booth['boothNumber'],
        "bootherPosessiveDisplayname" => getPossessiveDisplayName($booth['username']),
        "static" => True,
        "prev_booth_url" => $prevBoothUrl,
        "next_booth_url" => $nextBoothUrl
    ));
    $pageFrame->body($html);
    return $pageFrame->render();
}

function zip_and_serve($username, $absolute_dir, $relative_dir) {
    $relative_zip = sprintf("/the_end/%s_booths.zip", $username);
    $zipname = "{$_SERVER['DOCUMENT_ROOT']}".$relative_zip;
    touch($zipname);

    $zip = new ZipArchive;
    if ($zip->open($zipname, ZipArchive::CREATE)!==TRUE) {
        exit("Critical Error: Z");
    }
    if ($handle = opendir($absolute_dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && !strstr($entry,'.php')) {
                $file = sprintf('%s/%s', $absolute_dir, $entry);
                if (!file_exists($file)) {
                    die ("Critical Error: F");
                }
                $zip->addFile($file, sprintf('%s/%s', $username, $entry));
            }
        }
        closedir($handle);
    }

    $zip->close();

    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename='".base().$relative_zip."'");
    header('Content-Length: ' . filesize($zipname));
    header("Location: ".base().$relative_zip);
}
<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_lib('h2o-php/h2o');

error_reporting(E_ALL);

session_start();

if (!isLoggedIn()) {
    echo "Must Be Logged In";
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
    'boothername' => $username,
    'numperpage' => 10000
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

$absolute_temp_storage_dir = sprintf("{$_SERVER['DOCUMENT_ROOT']}%s", $relative_dir);

$absolute_zip_dir = sprintf("{$_SERVER['DOCUMENT_ROOT']}%s", "/the_end/download");

if (!file_exists($absolute_temp_storage_dir) && !mkdir($absolute_temp_storage_dir, 0755, true)) {
    echo "Critical error";
    return;
}



$next_page_num = 0;
# TODO: previous page num

$i = 0;

echo "We will now create snapshots for ".count($result['success']['booths']).
    " booths.<br/>You will be able to download them when this is finished.<hr/>";

foreach ($result['success']['booths'] as $booth) {
    $i++;
    echo $i." - Generating snapshot of booth #".$booth['boothnum']."<br/>";
    try {
        $page = build_css_block('pageframe');
        $page .= build_css_block('oneBooth-page');
        $page .= build_css_block('booth');
        $page .= build_css_block('posts');
        $page .= build_css_block('textcomment-nocontext');
        $page .= build_css_block('photocomment-nocontext');
        $page .= "<style>
    #booth_buttons {
        visibility: visible !important;
    }
</style>";
        $page .= build_standalone_page($strCookie, $booth, $next_page_num, 0);
        $next_page_num = $booth['boothnum'];

        $out_file = sprintf("%s/%s.html", $absolute_temp_storage_dir, $booth['boothnum']);
        file_put_contents($out_file, $page);
        syslog(LOG_INFO, "Wrote booth file to " . $out_file);
    } catch (Exception $e) {
        echo "There was a problem";
        death($e->getMessage());
        break;
    }
}

echo "<hr/>";

zip_and_serve($username, $absolute_temp_storage_dir, $hash);

function build_css_block($file_name) {
    $css_as_string = file_get_contents(sprintf("{$_SERVER['DOCUMENT_ROOT']}/css/%s.css", $file_name));
    return sprintf("<style>%s</style>", $css_as_string);
}

function img_to_base64_src($image_location, $filetype) {
    $im = file_get_contents($image_location);
    $imdata = base64_encode($im);
    $image_src = sprintf('data:image/%s;base64,%s', $filetype, $imdata);
    return $image_src;
}

function build_standalone_page($strCookie, $booth, $next_page_num, $prev_page_num) {
    $pageFrame = new PageFrame();
    $pageFrame->makeStatic();

    $filetype = $booth['filetype'];
    $image_file = sprintf("{$_SERVER['DOCUMENT_ROOT']}/booths/%s.%s", $booth['imageHash'], $filetype);
    $image_src = img_to_base64_src($image_file, $filetype);
    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/booth/templates/oneBooth.h2o");
    $boothBody = $htmlBuilder->render(array(
        'blurb' => $booth['blurb'],
        'boothImageUrl' => $image_src
    ));

    $prevBoothUrl = sprintf("%s.html", $prev_page_num);
    $nextBoothUrl = sprintf("%s.html", $next_page_num);

    $commentsBody = buildComments($strCookie, $booth['boothnum']);

    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/user-pages/templates/oneBoothFrame.h2o");
    $html = $htmlBuilder->render(array(
        "baseUrl" => '',
        "allowed" => True,
        "isOwner" => False,
        "commentInput" => "",
        "commentsBody" => $commentsBody,
        "boothBody" => $boothBody,
        "username" => $booth['username'],
        "boothNumber" => $booth['boothnum'],
        "bootherPosessiveDisplayname" => getPossessiveDisplayName($booth['username']),
        "static" => True,
        "prev_booth_url" => $prevBoothUrl,
        "next_booth_url" => $nextBoothUrl
    ));
    $pageFrame->body($html);
    return $pageFrame->render();
}

function buildComments($strCookie, $boothNum) {

    $url = base()."/_mobile/v2/getcomments.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
        'boothnum' => $boothNum
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
        echo("<p>$$$$$ ERROR LOADING COMMENTS FOR BOOTH \#".$boothNum."</p>");
        return "";
    }

    $comments = $result['success']['comments'];

    $commentsBody = "";
    //TODO: For loop goes here
    foreach ($comments as $comment) {

        $iconUrl = "";
        $imageUrl = "";
        $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/textCommentNoContext.h2o");
        if ($comment['mediaType'] == 'photo') {
            $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/comment/templates/photoNoContext.h2o");
            $iconFileLocation = sprintf("{$_SERVER['DOCUMENT_ROOT']}/%s", $comment['imageHash']);
            $imageUrl = img_to_base64_src($iconFileLocation, 'png');
        } else {
            $iconFileLocation = sprintf("{$_SERVER['DOCUMENT_ROOT']}/%s", $comment['iconImage']);
            $iconUrl = img_to_base64_src($iconFileLocation, 'png');
        }

        $commentBody = $htmlBuilder->render(array(
            "baseUrl" => "",
            "username" => $comment['commentername'],
            "iconUrl" => $iconUrl,
            "imageUrl" => $imageUrl,
            "displayName" => $comment['commenterdisplayname'],
            "text" => $comment['commenttext'],
            "canDelete" => False,
        ));
        $commentsBody .= $commentBody;
    }

    return $commentsBody;
}

function zip_and_serve($username, $absolute_dir, $hash) {
    $relative_dir = sprintf("/the_end/download/%s", $hash);
    $absolute_zip_dir= "{$_SERVER['DOCUMENT_ROOT']}".$relative_dir;

    if (!file_exists($absolute_zip_dir) && !mkdir($absolute_zip_dir, 0755, true)) {
        exit("Critical error ZD");
    }

    $relative_zip = sprintf("%s/%s_booths.zip", $relative_dir, $username);
    $absolute_zip = "{$_SERVER['DOCUMENT_ROOT']}".$relative_zip;
    touch($absolute_zip);

    $zip = new ZipArchive;
    if ($zip->open($absolute_zip, ZipArchive::CREATE)!==TRUE) {
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

    $file_location = base() . $relative_zip;
    echo "Please continue to ".$file_location;
//    header('Content-Type: application/zip');
//    header("Content-Disposition: attachment; filename='" . $file_location ."'");
//    header('Content-Length: ' . filesize($absolute_zip));
//    header("Location: ".base().$relative_zip);
}
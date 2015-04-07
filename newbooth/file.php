<?PHP

	require_once("{$_SERVER['DOCUMENT_ROOT']}/common/user_utils.php");
	require_once("{$_SERVER['DOCUMENT_ROOT']}/newbooth/mobile_utils.php");
	redirectIfMobile();
	
    require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_lib("h2o-php/h2o");

    $page = new PageFrame();
    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/newbooth/templates/fileUpload.mst");
    $html = $htmlBuilder->render(array(
        "baseUrl" => base()
    ));
    $page->css(base()."/css/file.css");
    $page->script(base()."/newbooth/jpeg_encoder_basic.js");
    $page->script(base()."/newbooth/jpeg_encoder_threaded.js");
    $page->script(base()."/newbooth/jpeg_encoder_threaded_worker.js");
    $page->script("//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js");
    $page->script(base()."/newbooth/jquery.FileReader.js");
    $page->body($html);
    $page->echoHtml();

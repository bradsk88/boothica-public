<?PHP 
	include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
    echo "
		<link rel = 'stylesheet' href = '/css/capture.css'  type='text/css' media='screen' />
	";
	include("{$_SERVER['DOCUMENT_ROOT']}/content/top.php");
    main();
    include("{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php");

    function main() {
        if (!isset($_SESSION['username'])) {
            go_to_login();
            return;
        }

        echo "
			<script language=\"JavaScript\" src=\"/webcam/webcam1.js\"></script>
        ";

            $protocol = strpos($_SERVER['SERVER_SIGNATURE'], '443') !== false ? 'https://' : 'http://';
            $url = $protocol . $_SERVER['HTTP_HOST'];
            echo "
                            <script type='text/javascript' src='{$url}/common/cookies.js'>
                            </script>
        ";

            echo "

                    <center>
                    <div class = 'camerasection' style = 'position: relative;'>
                        <div class = \"camera\" style = \"height: 0px; background: cyan;\" id = \"preview\">
                        </div>
                        <div class = 'camera' id=\"webcam\">
                            <embed id = 'flash' src = \"/webcam/webcam.swf\" width = 640 height = 480 />
                        </div>
                        <div id = 'countDown' class = 'countdown'>
                        </div>
                    </div>
                    <div style = \"width: 80%;\">
                        <form id = \"boothform\" action = \"/account/publiciconuploadfromcam\" method = \"post\">
                            <div id = 'cam_buttons'>
                                <button id = \"leftbtn\" class = medbutton type=button>3, 2, 1 ...</button>
                                <button id = \"rightbtn\" class = medbutton type=button>Snap!</button>
                            </div>

                            <div style='width: 0px; height: 0px; visibility: hidden;'>
                                <textarea id=\"image\"  name = \"image\" ></textarea>
                            </div>
                        </form>
                        <div id = 'status' style = 'height: 32px;'></div>
                    </div>
                    </center>
        ";

    }

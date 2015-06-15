
function openSnapNewBooth() {
    window.scrollTo(0, 0);
    var newLocation = window.location.pathname.split( '/' )[0]+"/newbooth/webcam";
    console.debug("Curloc: " + window.location.pathname + " newLoc: " + newLocation);
    if (window.location.pathname != newLocation) {
        window.history.pushState("string", "Title", newLocation);
    }
    $("#centerpane").html(
        "<script language=\"JavaScript\" src=\"/webcam/webcam.js?version=1\"></script>" +
        "<script type='text/javascript' src='/common/cookies.js'></script>" +
        "<center>" +
            "<div class = \"plainbutton\" style = \"margin-bottom: 4em;\" onclick=\"openFileNewBooth()\"> ... or Upload From File</div>" +
            "<a href = '/legacy/capturebeta'>" +
                "<div class = 'subheader' style = 'position: relative; width: 640px;' >" +
                    "If your browser doesn't load this page correctly.  Click here." +
                "</div>" +
            "</a>" +
            "<div class = 'camerasection' style = 'position: relative;'>" +
                "<div class = \"camera\" style = \"height: 0; background: cyan;\" id = \"preview\">" +
                "</div>" +
                "<div class = 'camera' id=\"webcam\">" +
                    "<embed id = 'flash' src = \"/webcam/webcam.swf\" width = 640 height = 480 />" +
                "</div>" +
                "<div id = 'countDown' class = 'countdown'>" +
                "</div>" +
            "</div>" +
            "<div style = \"width: 80%;\">" +
                "<form id = \"boothform\" action = \"/newbooth/file_upload\" method = \"post\">" +
                    "<div id = 'cam_buttons'>" +
                        "<button id = \"leftbtn\" class = \"medbutton plainbutton\" type=button>3, 2, 1 ...</button>" +
                        "<button id = \"rightbtn\" class = \"medbutton plainbutton\" type=button>Snap!</button>" +
                    "</div>" +
                    "<div style='width: 0; height: 0; visibility: hidden;'>" +
                        "<textarea id=\"image\"  name = \"image\" ></textarea>" +
                    "</div>" +
                    "<textarea id=\"blurb\" name = \"blurb\" style='width: 100%; height: 200px; resize: vertical;' ></textarea><br/>" +
                    "<input type = \"radio\" name = \"friendonly\" value = false>Make this booth \"Friends Only\" (Work In Progress)</input><br/>" +
                "</form>" +
                "<div id = 'status' style = 'height: 32px;'></div>" +
            "</div>" +
        "</center>");
    reset_buttons();
}

function openFileNewBooth() {
    window.scrollTo(0, 0);
    var newLocation = window.location.pathname.split( '/' )[0]+"/newbooth/file";
    console.debug("Curloc: " + window.location.pathname + " newLoc: " + newLocation);
    if (window.location.pathname != newLocation) {
        window.history.pushState("string", "Title", newLocation);
    }
    $("head").append(
        "<link rel='stylesheet' href='/css/file.css' type='text/css' media='screen' />" +
        "<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_basic.js\"></script>" +
        "<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_threaded.js\"></script>" +
        "<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_threaded_worker.js\"></script>" +
        "<script language=\"JavaScript\" src=\"//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js\"></script>" +
        "<script type = \"text/javascript\" src = \"/newbooth/jquery.FileReader.js\"></script>" +
        "<script type = \"text/javascript\" src = \"/newbooth/file.js\"></script>");
    $("#centerpane").html(
        "<center>" +
            "<a href = '/uploadbooth'>" +
                "<div class = 'subheader' style = 'position: relative; width: 640px;' >" +
                "If you have problems with this page, click here to access the old upload page..." +
                "</div>" +
            "</a>" +
            "<div id = \"previewspot\" style = \"width: 640px; height: 280px; border: 1px dashed black; box-shadow: 0 0 5px #AAAAAA;\"></div>" +
            "<div style = \"width: 80%;\">" +
                "<form id = \"uploadform\" method = null>" +
                    "<div id = \"fileselectsection\" style = \"position: absolute; opacity: 0; z-index: -1; width: 0px; height: 0px;\">" +
                        "<input type=\"file\" name=\"file\" id=\"file\" onChange = \"showPreviewAuto(this.files, false)\" />" +
                    "</div>" +
                    "<div id = \"buttonspot\"></div>" +
                    "<div class = \"greensection\">" +
                        "<input type = \"radio\" name = \"rotation\" id = \"0\" value = 0 checked />" +
                        "<label class = \"selected\" for=\"0\"><h1>0&deg;</h1></label>" +
                        "<input type = \"radio\" name = \"rotation\" id = \"90\"  value = 90 />" +
                        "<label for=\"90\"><h1>90&deg;</h1></label>" +
                        "<input type = \"radio\" name = \"rotation\" id = \"180\"  value = 180 />" +
                        "<label for=\"180\"><h1>180&deg;</h1></label>" +
                        "<input type = \"radio\" name = \"rotation\" id = \"270\"  value = 270 />" +
                        "<label for=\"270\"><h1>270&deg;</h1></label>" +
                    "</div>" +
                    "<textarea style='width: 100%; height: 240px;' id = 'blurb' name='blurb' placeholder='Write a blurb to go with this picture'></textarea><br />" +
                    "<div class = 'bigbutton' id = 'submit_button'>Upload Booth</div>" +
                    "<progress id = \"progress\" max=\"100\" value=\"0\" style = \"width: 100%\"></progress>" +
                "</form>" +
                "<div id = 'status' style = 'height: 32px;'></div>" +
            "</div>" +
        "</center>");
    initFileBoothUpload();
}

var submitDefaultColor;
var defaultPreviewHeight;
var imgWidth = 640;
var imgHeight;
var lastfiles;
var canvas;
var wasmobile = false;

var initialized = false;

function initFileBoothUpload(requestHash) {
    if (initialized) {
        return;
    }
    window.requestHash = requestHash;
    submitDefaultColor = $('#submit_button').css('background-color');
    defaultPreviewHeight = $('#preview_region').css('height');
    $('#submit_button').css('background-color', '#AAAAAA').css('cursor', 'default').text('Select an image first');

    $('#uploadform input:radio').addClass('input_hidden');
    $('#uploadform label').unbind('click');
    $('#uploadform label').bind('click', function () {
        $(this).addClass('selected').siblings().removeClass('selected');
        if (!lastfiles) {
            return;
        }
        var rotation = $('#' + $(this).attr('for')).val();
        showPreview(lastfiles, false || wasmobile, rotation);
    });

    if (fileReaderAlternativeUsed) {
        console.log("alternative");
        $('#status').html("This browser may not be fully supported.").slideDown(1000, 32);
        var button = document.createElement('button');
        button.type = "button";
        button.id = "select_file_button";
        button.innerHTML = "Click to select image...";
        $('#buttonspot').empty();
        $('#buttonspot').append(button);
        $('#select_file_button').addClass('bigbutton');
        $('#select_file_button').css('display', 'inline-block');
        $('#select_file_button').css('min-width', '100%');
        $('#select_file_button').css('margin', 0);
        $('#select_file_button').fileReader({
            id: 'fileReaderSWFObject',
            filereader: 'files/filereader.swf',
            debugMode: false
        });
        $(button).bind('change', function (evt) {
            $('#submit_button').attr('disabled', 'disabled');
            showPreviewAuto(evt.target.files, wasmobile);
        });
    } else {
        console.log("standard");
        var button = document.createElement('button');
        button.type = "button";
        button.id = "select_file_button";
        button.innerHTML = "Click to select image...";
        $('#buttonspot').empty();
        $('#buttonspot').append(button);
        $('#select_file_button').addClass('bigbutton');
        $('#select_file_button').bind('click', function (evt) {
            $("#hidden_file_selector").click();
        });
    }
    initialized = true;
}

function openUploadDialog() {
    $('#fileReaderSWFObject').click();
}

function getCurrentRotation() {
    return $('input[name=rotation]:checked', '#uploadform').val();
}

//remove all preview functions and use common/preview_scripts.js

function showPreviewAuto(files, mobile, requestHash) {
    var rotation = getCurrentRotation();
    showPreview(files, mobile, rotation, requestHash);
}

function repositionFlashButton() {
    var offset = $('#select_file_button').offset();
    $('#fileReaderSWFObject').offset({ top: offset.top, left: offset.left});
}

function doShowPreview(reader, rotation, mobile) {
    console.log("Mobile version: " + mobile);

    var tempImg = new Image();
    tempImg.src = reader.result;
    tempImg.onload = function() {
        console.log("img.onload entered");
        var proportion = this.width/this.height;
        imgHeight = this.height;
        console.log("Height: " + imgHeight);
        if (proportion > 3 || proportion < 0.333) {
            console.log("Proportions exceeded");
            $('#submit_button').unbind('click');
            $('#submit_button').css('background-color', '#FFAAAA').text('Image is too wide or too tall').css('cursor','default');
            $('#status').html("Width: " + imgWidth + ", Height: " + imgHeight).show(1000);
            $('canvas').remove();
            $('#preview_region').css('height',defaultPreviewHeight);
            repositionFlashButton();
            return;
        } else {
            console.log("Proportions OK");
            $('#submit_button').css('background-color', submitDefaultColor).text('Upload Booth').css('cursor','pointer');
            $('#submit_button').unbind('click');
            $('#submit_button').bind('click', function() {
                upload();
            });
            $('#status').hide(1000);
        }
        var MAX_WIDTH1 = 640; // This is not the width used for this preview.  This is the width used for saving.

        canvas = document.createElement('canvas');
        canvas.setAttribute('id','preview');
        canvas.width = tempImg.width;
        canvas.height = tempImg.height;
        if (rotation == 90 || rotation == 270) {
            canvas.width = tempImg.height;
            canvas.height = tempImg.width;
        }

        canvas.height *= MAX_WIDTH1 / canvas.width;
        canvas.width = MAX_WIDTH1;

        var ctx = canvas.getContext("2d");
        var x = canvas.width / 2;
        var y = canvas.height / 2;
        console.log("Canvas: " + x + " x " + y);
        ctx.translate(x, y);
        var angleInRadians = convertToRadians(rotation);
        console.log("Rotating " + angleInRadians + " rad (" + rotation + ")");

        ctx.rotate(angleInRadians);
        if (rotation == 90 || rotation == 270) {
            var drawHeight = -canvas.height / 2;
            var drawWidth = -canvas.width / 2;
            ctx.drawImage(this, drawHeight, drawWidth, canvas.height, canvas.width);
            console.log('Drawing with: ' + this + ", " + drawHeight + ", " + drawWidth + ", " + canvas.height + ", " + canvas.width);
        } else {
            console.log("Context: " + ctx);
            var drawHeight = -canvas.width / 2;
            var drawWidth =  -canvas.height / 2;
            ctx.drawImage(this, drawHeight, drawWidth, canvas.width, canvas.height);
            console.log('Drawing with: ' + this + ", " + drawHeight + ", " + drawWidth + ", " + canvas.height + ", " + canvas.width);
        }
        ctx.rotate(-angleInRadians);
        ctx.translate(-x, -y);

        $('#preview_region').empty();

        var vwidth = $('#preview_region').width();
        var canvas2 = document.createElement('canvas');
        canvas2.width = vwidth;
        canvas2.height = vwidth*canvas.height/canvas.width;
        var ctx2 = canvas2.getContext("2d");
        ctx2.drawImage(canvas, 0, 0, canvas2.width, canvas2.height);
        $('#preview_region').append(canvas2);
        $('#preview_region').css('height', canvas2.height);
        $('#fileReaderSWFObject').offset({ top: offset.top, left: offset.left});
        repositionFlashButton();
    }
}

function showPreview(files, mobile, rotation) {

    wasmobile = wasmobile || mobile
    if (files && files[0]) {

        if (files[0].type == 'image/png' || files[0].type == 'image/gif' || files[0].type == 'image/jpeg') {
            tryShowPreview( files, mobile, rotation);
        } else if (files[0].type === '') {
            alert( "Couldn't determine the file type for this photo.  Attempting to upload as JPEG");
            tryShowPreview( files, mobile, rotation);
        } else {
            alert(files[0].type.substring(6)+' is not an acceptable filetype.  Use jpg, png or gif.');
        }
    } else {
        alert("We were unable to accept this file.  If it is a standard image format (jpg, gif, png) please notify an adminstrator so they can remedy this.\nThank You!");
    }
}

function tryShowPreview( files, mobile, rotation) {
    lastfiles = files;
    if (typeof FileReader !== 'undefined') {
        var reader = new FileReader();
        if (fileReaderAlternativeUsed) {
            console.log("Using FileReader alternative");
            reader.addEventListener('loadend', function(evt) {
                doShowPreview(reader, rotation, mobile);
            });
        } else {
            console.log("Browser supports FileReader");
            reader.onload = function() {
                doShowPreview(reader, rotation, mobile);
            }
        }
        reader.readAsDataURL(files[0]);
    } else {
        alert("Your device is unsupported");
        console.log("Fatal error: No FileReader available");
    }
}


function convertToRadians(degree)
{
    var a = Math.PI/180;
    var rads = degree*a;
    return rads;
}

function upload(file) {

    /*
     * Backup for browsers that don't support toDataURL
     */
    var tdu = HTMLCanvasElement.prototype.toDataURL;
    HTMLCanvasElement.prototype.toDataURL = function(type)
    {
        var res = tdu.apply(this,arguments);
        //If toDataURL fails then we improvise
        if(res.substr(0,6) == "data:,")
        {
            var encoder = new JPEGEncoder();
            return encoder.encode(this.getContext("2d").getImageData(0,0,this.width,this.height), 90);
        }
        else {
            return res;
        }
    };

    $('#submit_button').text('Uploading').css('background-color', '#AAAAAA').css('cursor','default');
    $('#submit_button').unbind('click');

    var dataURL = canvas.toDataURL(lastfiles[0].type);

    $.post("{{baseUrl}}/_mobile/v2/postbooth.php", {
        image: dataURL,
        blurb: $('#blurb').val(),
        requestHash: window.requestHash
    }, function(data) {
        if ("undefined" === typeof(data.success)) {
            if (data.error) {
                $("#submit_button").prepend("Error: "+data.error);
                return;
            }
            $("#submit_button").prepend("There was a problem with the connection");
            return;
        }
        $('#submit_button').unbind('click');
        $('#submit_button').text('Done!  Redirecting you now...').css('cursor','pointer');
        location.href = data.success.boothUrl;
    }, "json")
    .fail(function(o, e) {
        $("#submit_button").prepend("Unexpected exception [" + e + "]");
    });
    $('#submit_button').text('Sending image...');

}
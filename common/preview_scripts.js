function showPreviewAuto(files, mobile) {
    var rotation = getCurrentRotation();
    showPreview(files, mobile, rotation);
}

function repositionFlashButton() {
    var offset = $('#select_file_button').offset();
    $('#fileReaderSWFObject').offset({ top: offset.top, left: offset.left});
}

function doShowPreview(reader, upload, rotation, mobile, submitbutton, previewSpot, dataURLHandler) {
    console.log("Mobile version: " + mobile);

    var tempImg = new Image();
    tempImg.src = reader.result;
    tempImg.onload = function() {
        console.log("img.onload entered");
        var proportion = this.width/this.height;
        imgHeight = this.height;
        console.log("Height: " + imgHeight);
        if (proportion > 3 || proportion < 0.333) {
            alert("Bad size");
            console.log("Proportions exceeded");
//            $(submitbutton).unbind('click');
//            $(submitbutton).css('background-color', '#FFAAAA').text('Image is too wide or too tall').css('cursor','default');
//            $('#status').html("Width: " + imgWidth + ", Height: " + imgHeight).show(1000);
//            $('canvas').remove();
//            $('#previewspot').css('height',defaultPreviewHeight);
//            repositionFlashButton();
            //TODO: Is flash button still necessary?
            return;
        } else {
            console.log("Proportions OK");
            $(submitbutton).css('background-color', "gray").text('Upload Booth').css('cursor','pointer');
            $(submitbutton).unbind('click');
            $(submitbutton).bind('click', upload);
        }
        var MAX_WIDTH1 = 320;

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

        $(previewSpot).empty();

        if (mobile) {
            var vwidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;
            var canvas2 = document.createElement('canvas');
            canvas2.width = vwidth;
            canvas2.height = vwidth*canvas.height/canvas.width;
            var ctx2 = canvas2.getContext("2d");
            ctx2.drawImage(canvas, 0, 0, canvas2.width, canvas2.height);
            $(previewSpot).append(canvas2);
            $(previewSpot).css('height', canvas2.height);
            $('#fileReaderSWFObject').offset({ top: offset.top, left: offset.left});
        } else {
            $(previewSpot).append(canvas);
            $(previewSpot).css('height', canvas.height);
        }
        //repositionFlashButton();

        canvasFallback();

        if (dataURLHandler != null) {
            console.log("DataURLHandler OK");
            var data = canvas.toDataURL(lastfiles[0].type);
            dataURLHandler(data);
        } else {
            console.log("DataURLHandler was null");
        }

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

function canvasFallback() {
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
    }
}
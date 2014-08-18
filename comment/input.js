/**
 * Created by Brad on 1/19/14.
 */

$('.commentaddpic > .webcambuttons').hide();
$('.commentaddpic > .filebuttons').hide();
$('.commentaddpic > .camresetbutton').hide();
$('.commentaddpic > .countdown').hide();

$(function() {

    $('.commentinputsection > .commentpostbuttonwrapper > .commentpostbutton').click( function () {
        commentSent(false);
    });

    $('.commentaddpic > .commentaddpicfwbutton').click( function() {
        $('.commentaddpic > .webcambuttons').slideToggle("slow");
        $('.commentaddpic > .commentaddpicfwbutton').slideToggle("slow");
        $('.commentinputsection > .commenttextarea').animate({width: '480px', marginLeft: '320px'}, function() {
            $('.photowrapper > .photocommentsection').animate({width: '320px'}, function() {
                readyCam();
            });
        });
    });

    if (window.screen.width < 1280) {
        readyFile();
    }

    $('#camcountdownbutton').click( function() {
        startCountDown();
    })

});

function readyFile() {
    $('#filetocambutton').click( function() {
        $('.commentaddpic > .webcambuttons').slideToggle("slow");
        $('.commentaddpic > .filebuttons').slideToggle("slow" , function() {
            $('#filetocambutton').unbind('click');
            readyCam();
        });
    });

    $('#filebrowsebutton').click( function() {
        $('input#fileinput').click();
    });

    $('#photocommentsection').remove();
    flushCam();
}

function showPreview(files) {
    //TODO: This is duplicated in newbooth/file.js
    if (files && files[0]) {

        if (files[0].type == 'image/png' || files[0].type == 'image/gif' || files[0].type == 'image/jpeg') {
            tryShowPreview( files, sendCommentByCam );
        } else if (files[0].type === '') {
            alert( "Couldn't determine the file type for this photo.  Attempting to upload as JPEG");
            tryShowPreview( files, sendCommentByCam );
        } else {
            alert(files[0].type.substring(6)+' is not an acceptable filetype.  Use jpg, png or gif.');
        }
    } else {
        alert("We were unable to accept this file.  If it is a standard image format (jpg, gif, png) please notify an adminstrator so they can remedy this.\nThank You!");
    }
}

function tryShowPreview( files, upload ) {
    var rotation = 0;
    var submitbutton = "#commentpostbutton"
    var previewSpot = '.photowrapper > .photocommentsection';

    lastfiles = files;
    if (typeof FileReader !== 'undefined') {

        if (window.screen.width < 1280) {
            $('.piccancelbutton').one("click", function () {
                closeCam(true);
            });
            $('.commentinputsection > .commenttextarea').animate({width: '480px', marginLeft: '320px'}, function() {
                $('.photowrapper > .photocommentsection').animate({width: '320px'}, function() {
                    $('.commentinputsection > .commentpostbuttonwrapper > .commentpostbutton').click( function () {
                        commentSent(true);
                    });
                });
            });
        }

        var reader = new FileReader();
        if (fileReaderAlternativeUsed) {
            console.log("Using FileReader alternative");
            reader.addEventListener('loadend', function(evt) {
                doShowPreview(reader, upload, rotation, false, submitbutton, previewSpot, function(dataURI) {
                    $('#image').val(dataURI);
                });
            });
        } else {
            console.log("Browser supports FileReader");
            reader.onload = function() {
                doShowPreview(reader, upload, rotation, false, submitbutton, previewSpot, function(dataURI) {
                    $('#image').val(dataURI);
                });
            }
        }
        reader.readAsDataURL(files[0]);
    } else {
        alert("Your device is unsupported");
        console.log("Fatal error: No FileReader available");
    }
}

function readyCam() {

    $('#camtofilebutton').click( function() {
        $('.commentaddpic > .webcambuttons').slideToggle("slow");
        $('.commentaddpic > .filebuttons').slideToggle("slow", function() {
            $('#camtofilebutton').unbind('click');
            readyFile();
        });
    });

    $('.piccancelbutton').one("click", function () {
        closeCam(true);
    });
    $('.commentinputsection > .commentpostbuttonwrapper > .commentpostbutton').click( function () {
        commentSent(true);
    });
    var flashvars = {};
    var params = {};
    params.allowscriptaccess = "always";
    var attributes = { };
    attributes.id = "photocommentsection";
    swfobject.embedSWF("http://boothi.ca/webcam/webcam.swf", "photocommentsection", "320", "240", "9", false, flashvars, params, attributes, function(e) {

        $('#camsnapbutton').click( function() {
            snap();
        });

    });
}

function getCam() {
    return document.getElementById("photocommentsection");
}

function snap() {
    getCam().snapJPG();
    $('.commentaddpic > .camresetbutton').show();
    $('.commentaddpic > .webcambuttons').hide();
    $('.commentaddpic > .camresetbutton').click( function() {
        $('.commentaddpic > .camresetbutton').hide();
        $('.commentaddpic > .countdown').hide();
        $('.commentaddpic > .webcambuttons').show()
        readyCam();
    });
}


function startCountDown() {
    var i = 3;
    $('.commentaddpic > .webcambuttons').hide();
    $('.commentaddpic > .countdown').show();
    var pause = 1000;
//
    var countDownObj = $('.commentaddpic > .countdown');
    countDownObj.count = function(i) {
        countDownObj.text(i);
        if (i <= 0) {
            snap();
//            $('#leftbtn').css('background', submitDefaultColor);
//            $('#leftbtn').css('cursor', 'pointer');
//            $('#countDown').css('opacity', '0');
            return;
        }
        setTimeout(function() {
                countDownObj.count(i - 1);
            },
            pause);

    }

    countDownObj.count(i);
}

//called by webcam.swf
function recieveTextFromFlash(Txt) {
    var img = new Image();
    var dataURI = "data:image/jpeg;base64," + Txt;
    img.src = dataURI;
    img.width = 320;
    img.height = 240;
    img.id = "previewpic";
    flushCam();
    $('.photowrapper > .photocommentsection').append(img);
    $('#image').val(dataURI);
    buttonsResetUpload();
}

function flushCam() {
    swfobject.removeSWF("photocommentsection");
    $('.photowrapper').append("<div class='photocommentsection' id = 'photocommentsection'></div>");
    $('#previewpic').remove();
}

function buttonsResetUpload() {
    //TODO: Change the buttons
}

function sendCommentByCam() {
    commentSent(true);
}

function commentSent(bycam) {

    $('#comment_submit').attr('disabled', 'disabled');
    $('#comment_form').submit();
    var target = $('#comment_target');
    target.animate({ height: 30 }, 600);
    target.contents().find('body').html('Uploading Comment...');
    target.load(function() {
        target_use_complete();
        closeCam(bycam);
    });
}

function closeCam(bycam) {
    if (bycam) {
        $('#photocommentsection').remove();
        flushCam();
    }
    $('#image').val("");
    initButtons();
    $('.commentinputsection > .commenttextarea').animate({width: '800px', marginLeft: '0'}, function() {
        $('.photowrapper > .photocommentsection').animate({width: '0'});
    });
}

function initButtons() {
    $('.commentaddpic > .camresetbutton').hide();
    $('.commentaddpic > .webcambuttons').hide();
    $('.commentaddpic > .filebuttons').hide();
    $('.piccancelbutton').one(function () {
        //do nothing
    });
    $('.commentaddpic > .countdown').hide();
    if (window.screen.width > 1280) {
        $('.commentaddpic > .commentaddpicfwbutton').show();
    }
}
var submitDefaultColor;
var defaultColor = "#AAAAAA";

//called by webcam.swf
function recieveTextFromFlash(Txt) {
	var img = new Image();
	var dataURI = "data:image/png;base64," + Txt;
	img.src = dataURI;
	img.width = 640;
	img.height = 480;
	$("#preview").empty();
	$("#preview").append(img);
	$('#image').text(dataURI);
	buttonsResetUpload();
}

$(function() {

	submitDefaultColor = $('#leftbtn').css('background-color');
	buttons_reset();

});

function snap() {
	document.getElementById('flash').snap();
}

function buttonsResetUpload() {
	$('#leftbtn').unbind('click');
	$('#rightbtn').unbind('click');
	$('#leftbtn').one('click', function() {
		webcam_reset();
	});
	$('#rightbtn').one('click', function() {
		webcam_upload();
	});
	$('#leftbtn').text('Reset');
	$('#rightbtn').text('Upload');
	$('#preview').css("visibility","visible");
	$('#webcam').css("visibility","hidden");
}

function settings_open() {
	
	var username=getCookie('username');
	
	if (username==null || username =='') {
	
		//todo:
		//webcam.configure( 'privacy' );

	}

}	

function webcam_reset() {
	document.getElementById('flash').start();
	console.log("camera reactivated");
	buttons_reset();
}

function buttons_reset() {
	$('#leftbtn').unbind('click').one('click', function() {
	console.log("left button click");
		start_countdown(3);
	});
	
	$('#rightbtn').unbind('click').one('click', function() {
		console.log("right button click");
		snap();
	});
	
	$('#preview').css("visibility","hidden");
	$('#webcam').css("visibility","visible");
	
	$('#leftbtn').text('3, 2, 1...')
	$('#rightbtn').text('Snap!');
}

function webcam_upload() {
	$('#leftbtn').unbind('click');
	$('#rightbtn').unbind('click');
	$('#leftbtn').text('Uploading').css('cursor','default').css('background',defaultColor);
	$('#rightbtn').text('Uploading').unbind('click').css('cursor','default').css('background',defaultColor);
	$('#boothform').submit();
}

// LZW-compress a string
function lzw_encode(s) {
    var dict = {};
    var data = (s + "").split("");
    var out = [];
    var currChar;
    var phrase = data[0];
    var code = 256;
    for (var i=1; i<data.length; i++) {
        currChar=data[i];
        if (dict[phrase + currChar] != null) {
            phrase += currChar;
        }
        else {
            out.push(phrase.length > 1 ? dict[phrase] : phrase.charCodeAt(0));
            dict[phrase + currChar] = code;
            code++;
            phrase=currChar;
        }
    }
    out.push(phrase.length > 1 ? dict[phrase] : phrase.charCodeAt(0));
    for (var i=0; i<out.length; i++) {
        out[i] = String.fromCharCode(out[i]);
    }
    return out.join("");
}

function start_countdown(i) {

	$('#leftbtn').unbind('click');
	$('#leftbtn').css('background', defaultColor).css('cursor','default');
	$('#countDown').css('opacity', '1');
	var pause = 1000;
	
	var countDownObj = document.getElementById('countDown');
	countDownObj.count = function(i) {
		countDownObj.innerHTML = i;
		if (i <= 0) {
			snap();
			$('#leftbtn').css('background', submitDefaultColor);
			$('#leftbtn').css('cursor', 'pointer');
			$('#countDown').css('opacity', '0');
			return;
		}
		setTimeout(function() {
			countDownObj.count(i - 1);
		},
		pause);
	
	}
	
	countDownObj.count(i);
}
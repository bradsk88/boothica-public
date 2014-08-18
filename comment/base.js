/**
 * Created with JetBrains PhpStorm.
 * User: Brad
 * Date: 9/13/13
 * Time: 10:32 PM
 * To change this template use File | Settings | File Templates.
 */
var newestcomment = -1;
var pathArray = window.location.pathname.split( '/' );
var boothername = pathArray[2];

function open_report_user(user, commentnumber) {

    var popOverBox = document.getElementById('');
    var xmlhttp = openXmlHttp();
    var url = '/reportdialog?number=' + commentnumber;
    url = url + '&username=' + user;
    xmlhttp.onreadystatechange=function() {
        if(xmlhttp.readyState==4)
        {
            $('#popoverbox').html(xmlhttp.responseText);
        }
    }
    xmlhttp.open('GET',url,false);
    xmlhttp.send();

    loadScreen();
    var boxWidth = $('#popoverbox').css('width').replace("px", "");
    var boxHeight = $('#popoverbox').css('height').replace("px", "");
    var left = ((myWidth / 2)-(boxWidth / 2))+'px';
    var top = ((myHeight / 2)-(boxHeight / 2)+myScroll)+'px';
    $('#popoverbox').css('width','400px');
    $('#popoverbox').css('left',left);
    $('#popoverbox').css('top',top);
    $('#popoverbox').css('display','block');

}


function report_user(user, commentnumber) {

    var popOverBox = document.getElementById('popoverbox');
    var xmlhttp = openXmlHttp();
    var url = '/actions/report?number=' + commentnumber + '&reason=' + $('#reportreason').val();
    url = url + '&username=' + user;
    xmlhttp.onreadystatechange=function() {
        if(xmlhttp.readyState==4)
        {
            if (xmlhttp.responseText == 0) {

                popOverBox.innerHTML = 'Successful<br/><br/><a href="javascript:close_report_user()">Close</a>';

            } else {

                alert(xmlhttp.responseText);
                popOverBox.innerHTML = 'Failed<br/><br/><a href="javascript:close_report_user()">Close</a>';

            }

        }
    }
    xmlhttp.open('GET',url,false);
    xmlhttp.send();

}

function close_report_user() {
    var popOverBox = document.getElementById('popoverbox');
    popOverBox.style.display = 'none';
}

function ignore_user(username, sourceuser, sourcebooth) {
    var confirmed = confirm("Are you sure you want to ignore " + username + "?\n\nTheir activity will be invisible to you from now on.");
    if (confirmed) {
        location.href = "/ignoreuser?user="+username+"&bu="+sourceuser+"&bn="+sourcebooth;
    }
}

// window.setTimeout(function() {check_for_comments(5000);}, 5000);

function check_for_comments(delay) {

    var xmlhttp = openXmlHttp();
    var url = '/checkcomments';
    xmlhttp.onreadystatechange=function() {

        if(xmlhttp.readyState==4)
        {

            if(xmlhttp.responseText != newestcomment) {

                newestcomment = xmlhttp.responseText;
                update_comments();
                dullCounter = 0;

            } else {

                dullCounter++;

            }

            if (dullCounter < 10) {
                window.setTimeout(function() {check_for_comments(delay+1000);}, delay+1000);
            } else if (dullCounter < 100) {
                window.setTimeout(function() {check_for_comments(30000);}, 30000);
            }

        }

    }
    xmlhttp.open('GET',url,true);
    xmlhttp.send();

}

function target_use_complete() {
    reset_comment_area(true);
    update_comments();
    $('#comment_target').delay(4000).animate({ height: 0 }, 600);
}

function mention(username) {

    if (!$('textarea#commentarea').val()) {
        $('textarea#commentarea').val($('textarea#commentarea').val() + '@' + username + ' ');
    } else {
        $('textarea#commentarea').val($('textarea#commentarea').val() + '\n@' + username + ' ');
    }
    $('textarea#commentarea').focus();

}


function open_upload() {

    var xmlhttp = openXmlHttp();
    var prevText = $('textarea#commentarea').val();
    var url = '/commentinputfromfilesection.php?number=' + getBoothNumber() + '&boother=' + boothername;
    xmlhttp.onreadystatechange=function() {

        if(xmlhttp.readyState==4)
        {
            $('#commentinputsection').html(xmlhttp.responseText);
            $('#commentinputsection').animate({height: 180}, 200);
            $('textarea#commentarea').val(prevText);
            setupSuggest(window.suggests);
        }

    }
    xmlhttp.open('GET',url,true);
    xmlhttp.send();

}

function open_camera() {

    var xmlhttp = openXmlHttp();
    var prevText = $('textarea#commentarea').val();
    var url = '/commentinputfromcamerasection.php?number=' + getBoothNumber();
    xmlhttp.onreadystatechange=function() {

        if(xmlhttp.readyState==4)
        {
            $('#commentinputsection').html(xmlhttp.responseText);
            $('#commentinputsection').height(280);
            $('textarea#commentarea').val(prevText);
            setupSuggest(window.suggests);
        }

    }
    xmlhttp.open('GET',url,true);
    xmlhttp.send();

}


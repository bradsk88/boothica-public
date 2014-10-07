
function getFeedGridCellsHTML(data, pageNum, loadMoreButtonId, loadMoreFunction) {
    var html = "<div class = \"gridcontainer\" id = \"page"+pageNum+"\">";
    $.each(data, function (idx, obj) {
        if (idx == "error") {
            html = "Error:" + obj;
            return;
        }
        var bgImage = "/booths/" + obj.imageHash + "." + obj.filetype;
        var blurb = obj.blurb;
        var content = $('<div>' + blurb + '</div>');
        content.find('a').replaceWith(function() { return this.childNodes; });
        var blurb = content.html();
        blurb = truncate(blurb, 200, '');
//        var split = blurb.split("<br/>");
//        blurb = "";
//        for (var i = 0; i < Math.min(10, split.length); i++) {
//            blurb = blurb + split[i] + "<br/>"
//        }
//        blurb = blurb.substr(1, blurb.length - 3);
//        if (split.length > 10) {
//            blurb = blurb + "...";
//        }
        var iHTML =
            "<div class = \"narrowboothgridwrapper\">" +
                "<div class = \"narrowbooth\" onclick=\"openBooth(" + obj.boothnum + ")\">" +
                "<div class = \"narrowboothpadline\"></div>" +
                "<div class = \"narrowboothusername\">" + obj.bootherdisplayname + "</div>" +
                "<div class = \"narrowboothpad\"></div>" +
                "<div class = \"narrowboothcell\">" +
                "<div class = \"narrowboothaspect\"></div>" +
                "<div class = \"narrowboothimage\" style = \"background-image: url(" + bgImage + ")\"></div>" +
                "</div>" +
                "</div>" +
				"<div class = \"narrowboothtextwrapper\">" +
					"<div class = \"narrowbooth-text\">" + blurb + "</div>" +
					"<div class = \"narrowbooth-textshadow\"></div>" +
				"</div>" +
                "</div>";
        html = html + iHTML;
    });
    html = html +
        "<div style = 'clear: both;'></div>"+
        "</div>" +
        "<div class = \"plainbuttoninverted\" id = \""+loadMoreButtonId+"\" onclick=\""+loadMoreFunction+"\">" +
        "More..." +
        "</div>";
    return html;
}

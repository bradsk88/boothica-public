<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

/*
 * Finds usernames in a blurb and turns them into links0
 */
function handle_mentions($blurb) {
    return preg_replace_callback("/@([a-zA-Z0-9_-]*)/", 'linkify_mention', $blurb);
}

/*
 * Turns a username into a link.
 */
function linkify_mention($mentions) {
    $mention = $mentions[1];
    return "<a class = \"mention\" href = \"".base()."/users/".strtolower($mention)."/booths.php\">@".$mention."</a>";
}

/*
 * Finds usernames in a blurb and turns them into links0
 */
function handle_hashtags($blurb) {
    return preg_replace_callback("/#([a-zA-Z0-9_-]*)/", 'linkify_hashtag', $blurb);
}

/*
 * Turns a username into a link.
 */
function linkify_hashtag($mentions) {
    $mention = $mentions[1];
    return "<a class = \"hashtag\" href = \"".base()."/searchresults?scope=booth&q=%23".$mention."\">#".$mention."</a>";
}

/*
 * Finds URLs in a blurb and converts them to hyperlinks.
 */
function handle_links($blurb) {
    return preg_replace_callback("/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?������]))/", 'linkify_link', $blurb);
}

/*
 *Turns a URL into a hyperlink
 */
function linkify_link($links) {
    $link = $links[0];
    $fixedlink = $link;
    $checkforhttp = preg_match('/^https?:\\/\\/(.*)/', $link, $matches);
    if (count($matches) == 0) {
        $fixedlink = "http://".$link;
    }
    return "<a class = \"extLink\" href = '".$fixedlink."'>" . $link . "</a>";
}

/**
 * This function converts a plain text blurb to a formatted blurb by removing HTML, converting URLs into
 * anchors, converting hashtags into links and converting "@" usernames into links.
 * <p>
 * Also handles retention of whitespace characters.
 */
function formatBlurb($unformatted) {
    $formattedblurb = strip_tags($unformatted);
    $formattedblurb = handle_mentions($formattedblurb);
    $formattedblurb = handle_links($formattedblurb);
    $formattedblurb = handle_hashtags($formattedblurb);
    $formattedblurb = mysql_real_escape_string(str_replace("\n", "<br />", $formattedblurb));
    return $formattedblurb;
}

function getExtension($str) {

    $i = strrpos($str,".");
    if (!$i) { return ""; }

    $l = strlen($str) - $i;
    $ext = substr($str,$i+1,$l);
    return $ext;
}

function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}
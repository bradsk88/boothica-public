<?php

session_start();
error_reporting(0);
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");

    connect_to_boothsite();
    update_online_presence();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    else if (failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }

    $pagenum = 1;
    if (isset($_POST['pagenum'])) {
        $pagenum = $_POST['pagenum'];
    }

    $numperpage = 10;
    if (isset($_POST['numperpage'])) {
        $numperpage = $_POST['numperpage'];
    }

    $sql = "SELECT COUNT(*) as count FROM friendstbl WHERE fkUsername = '".$username.";'";
    $countres = mysql_query($sql);
    if (!$countres) {
        sql_death1($sql);
    }
    $row = mysql_fetch_array($countres);
    if ($row['count'] == 0) {
        echo json_encode(array());
        return;
    }

    $sql =
        "SELECT A.`fkUsername` as `commenter`,
            B.`fkUsername` as `boothername`,
            B.`pkNumber` as `boothnumber`,
            B.`imageTitle` as `boothImg`,
            B.`filetype` as `boothFileType`,
            C.`commentbody` as `comment`,
            C.`hasPhoto` as `hasPhoto`,
            C.`hash` as `commentImage`,
            C.`extension` as `commentExtension`,
            C.`imageHeightProp` as `imageHeightProp`,
            C.`pkCommentNumber` as `commentNum`
        FROM `activitytbl` A
        LEFT JOIN `commentstbl` C ON A.`fkIndex` = C.`pkCommentNumber`
		LEFT JOIN `boothnumbers` B ON C.`fkNumber` = B.`pkNumber`
        WHERE A.`fkUsername` IN (
                    SELECT `fkFriendName`
						FROM `friendstbl`
						WHERE `fkUsername` = '".$username."'
					)
            AND
            (
                '".$username."' IN (
                    SELECT `fkFriendName`
                    FROM `friendstbl`
                    WHERE `fkUsername` = A.`fkUsername`)
                OR
                A.`fkUsername` IN (
                    SELECT `fkUsername`
                    FROM `userspublictbl`)
            )
            AND
            (
                A.`type` = 'comment'
                AND
                (
                    (SELECT `fkUsername`
                    FROM `commentstbl`
                    WHERE `pkCommentNumber` = A.`fkIndex`
                    LIMIT 1)
                    NOT IN
                    (SELECT `fkIgnoredName`
                    FROM `ignorestbl`
                    WHERE `fkUsername` = '".$username."')

                    AND
                    (
                        (SELECT true
                        FROM `friendstbl`
                        WHERE `fkUsername` =
                            (SELECT `fkUsername`
                            FROM `boothnumbers`
                            WHERE `pkNumber` =
                                (SELECT `fkNumber`
                                FROM `commentstbl`
                                WHERE `pkCommentNumber` = A.`fkIndex`
                                LIMIT 1)
                            LIMIT 1)
                            AND `fkFriendName` = '".$username."'
                        LIMIT 1)

                        OR
                        (
                            (SELECT `fkUsername`
                            FROM `boothnumbers`
                            WHERE `pkNumber` =
                                (SELECT `fkNumber`
                                FROM `commentstbl`
                                WHERE `pkCommentNumber` = A.`fkIndex`
                                LIMIT 1))
                            IN
                            (SELECT `fkUsername`
                            FROM `userspublictbl`
                            )
                            AND (SELECT `isPublic` FROM `boothnumbers` WHERE `isPublic` = 1 AND `pkNumber` = (SELECT `fkNumber`
                                FROM `commentstbl`
                                WHERE `pkCommentNumber` = A.`fkIndex`
                                LIMIT 1) LIMIT 1)
                        )
                    )
                )
            )
        ORDER BY A.`datetime` DESC
        LIMIT " . $numperpage * ($pagenum - 1) . ", ".$numperpage.";";

    $result = mysql_query($sql);

    if (!$result) {
        echo json_encode(
            array("error" => mysql_death1($sql))
        );
        return;
    }

    $booths = array();
    while($row = mysql_fetch_array($result)) {
        $booths[] = array(
            'commentername' => $row['commenter'],
            'commenterdisplayname' => (string)getDisplayName($row['commenter']),
            'commenterImg' => (string) UserImage::getImage($row['commenter']),
            'comment' => $row['comment'],
            'boothIconImg' => "/booths/tiny/".$row['boothImg'].".".$row['boothFileType'],
            'boothnumber' => $row['boothnumber'],
            'boothername' => $row['boothername'],
            'hasPhoto' => ($row['hasPhoto']==1),
            'commentPhotoImg' => "/comments/".$row['commentImage'].".".$row['commentExtension'],
            'imageRatio' => $row['imageHeightProp'],
            'commentNum' => $row['commentNum']
        );
    }
    echo json_encode($booths);

}
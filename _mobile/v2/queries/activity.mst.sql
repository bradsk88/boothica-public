/*get visible activity */
SELECT
  commentBody as commentText
  , pkCommentNumber as commentNumber
  , C.fkUsername as commenterName
  , B.fkUsername as bootherName
  , C.fkNumber as boothNum
  , C.hasPhoto as hasMedia
  , CONCAT(C.hash, '.', C.extension) AS media
  , C.extension AS ext
  , A.datetime as datetime

FROM `activitytbl` Auserfeed
  LEFT JOIN `commentstbl` C ON A.`fkIndex` = C.`pkCommentNumber`
  LEFT JOIN `boothnumbers` B ON C.`fkNumber` = B.`pkNumber`
WHERE A.`fkUsername` IN (
  SELECT `fkFriendName`
  FROM `friendstbl`
  WHERE `fkUsername` = "{{username}}"
)
      AND
      (
        "{{username}}" IN (
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
           WHERE `fkUsername` = "{{username}}")

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
                   AND `fkFriendName` = "{{username}}"
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

{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}



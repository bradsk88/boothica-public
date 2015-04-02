SELECT A.fkUsername as commenter,
    B.fkUsername as boothername,
    B.pkNumber as boothnumber,
    B.imageTitle as boothImg,
    B.filetype as boothFileType,
    C.commentbody as comment,
    C.hasPhoto as hasPhoto,
    C.hash as commentImage,
    C.extension as commentExtension,
    C.imageHeightProp as imageHeightProp,
    C.pkCommentNumber as commentNum
FROM activitytbl A
LEFT JOIN commentstbl C ON A.fkIndex = C.pkCommentNumber
LEFT JOIN boothnumbers B ON C.fkNumber = B.pkNumber
WHERE A.fkUsername IN
(
  SELECT fkFriendName
  FROM friendstbl
  WHERE fkUsername = '{{username}}'
)
AND
(
  '{{username}}' IN
  (
    SELECT fkFriendName
    FROM friendstbl
    WHERE fkUsername = A.fkUsername
  )
  OR
  A.fkUsername IN
  (
    SELECT fkUsername
    FROM userspublictbl
  )
)
AND
(
  A.type = 'comment'
  AND
  (
    (
      SELECT
        fkUsername
      FROM
        commentstbl
      WHERE
        pkCommentNumber = A.fkIndex
      LIMIT
        1
    )
    NOT IN
    (
      SELECT fkIgnoredName
      FROM ignorestbl
      WHERE fkUsername = '{{username}}'
    )
    AND
    (
      (
        SELECT true
        FROM friendstbl
        WHERE fkUsername IN
        (
          SELECT fkUsername
          FROM boothnumbers
          WHERE pkNumber =
          (
            SELECT fkNumber
            FROM commentstbl
            WHERE pkCommentNumber = A.fkIndex
            LIMIT 1
          )
          LIMIT 1
        )
        AND fkFriendName = '{{username}}'
        LIMIT 1
      )
      OR
      (
        (
          SELECT fkUsername
          FROM boothnumbers
          WHERE pkNumber =
          (
            SELECT fkNumber
            FROM commentstbl
            WHERE pkCommentNumber = A.fkIndex
            LIMIT 1
          )
        )
        IN
        (
          SELECT fkUsername
          FROM userspublictbl
        )
        AND
        (
          SELECT isPublic
          FROM boothnumbers
          WHERE isPublic = 1
          AND pkNumber IN
          (
            SELECT fkNumber
            FROM commentstbl
            WHERE pkCommentNumber = A.fkIndex
            LIMIT 1
          )
        LIMIT 1
        )
      )
    )
  )
)
ORDER BY A.datetime DESC
LIMIT {{pageStartIndex}}, {{numPerPage}};
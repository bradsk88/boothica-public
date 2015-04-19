/*get visible activity */
SELECT
  commentBody as commentText
  , pkCommentNumber as commentNumber
  , C.fkUsername as commenterName
  , B.fkUsername as bootherName
  , C.fkNumber as boothNum
  , C.hasPhoto as hasMedia
  , CONCAT(C.hash, '.', C.extension) AS media
  , A.datetime as datetime
FROM
  commentstbl C
LEFT JOIN
  activitytbl A
ON
  C.pkCommentNumber = A.fkIndex
LEFT JOIN
  boothnumbers B
ON
  C.fkNumber = B.pkNumber
WHERE
  C.fkUsername = '{{username}}'
OR
  B.fkUsername = '{{username}}'
OR
(
  C.fkUsername
  IN
  (
    SELECT commenterName FROM (
        SELECT fkUserName as commenterName FROM friendstbl WHERE fkFriendName = '{{username}}'
      UNION (
        SELECT fkUserName as commenterName
        FROM usersprivacytbl
        WHERE (
          privacyDescriptor = 'public'
        {% if isLoggedIn %}
          OR privacyDescriptor = 'semi-public'
        {% endif %}
        )
      )
    ) A
    WHERE commenterName NOT IN (
      SELECT fkIgnoredName FROM ignorestbl WHERE fkUsername = '{{username}}'
    )
  )
  AND
    B.fkUsername
  IN
  (
    SELECT bootherName FROM (
        SELECT fkUserName as bootherName FROM friendstbl WHERE fkFriendName = '{{username}}'
    ) A
    WHERE bootherName NOT IN (
      SELECT fkIgnoredName FROM ignorestbl WHERE fkUsername = '{{username}}'
    )
  )
)
ORDER BY datetime DESC
LIMIT {{pageStartIndex}}, {{numPerPage}}

/* TODO: The order from this is weird for pubby.  Figure out why. */

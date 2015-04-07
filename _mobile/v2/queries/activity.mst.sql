/*get visible activity */
SELECT
  commentBody as commentText
  , pkCommentNumber as commentNumber
  , C.fkUsername as commenterName
  , B.fkUsername as bootherName
  , C.fkNumber as boothNum
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
(
  C.fkUsername
  IN
  (
    SELECT commenterName FROM (
        SELECT fkUserName as commenterName FROM friendstbl WHERE fkFriendName = '{{username}}'
      UNION (SELECT fkUserName as commenterName FROM userspublictbl)
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
      UNION (SELECT fkUserName as bootherName FROM userspublictbl)
    ) A
    WHERE bootherName NOT IN (
      SELECT fkIgnoredName FROM ignorestbl WHERE fkUsername = '{{username}}'
    )
  )
)
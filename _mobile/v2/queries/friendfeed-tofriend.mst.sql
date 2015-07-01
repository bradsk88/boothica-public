SELECT
  b.fkUsername,
  b.pkNumber,
  b.blurb,
  b.datetime,
  b.imageTitle,
  b.filetype,
  b.imageHeightProp
FROM boothnumbers b
WHERE b.fkUsername IN (
  SELECT fkFriendname FROM friendstbl WHERE lower(fkUsername) = '{{username}}'
)
AND '{{username}}' IN (
  SELECT fkFriendname FROM friendstbl WHERE fkUsername = b.fkUsername
)
AND '{{current_username}}' IN (
  SELECT fkFriendname FROM friendstbl WHERE fkUsername = b.fkUsername
)
OR (
  b.fkUsername IN
  (
    SELECT fkUsername FROM userspublictbl
  )
  AND b.fkUsername IN
  (
    SELECT fkUsername
    FROM usersprivacytbl
    WHERE fkUsername = b.fkUsername
    AND (privacyDescriptor = 'public' OR privacyDescriptor = 'semi-public')
    LIMIT 1
  )
)
          ORDER BY b.pkNumber DESC LIMIT " . $howMany * ($pageNum - 1) . ", ".$howMany.";
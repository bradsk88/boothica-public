SELECT
  COUNT(*) as count
FROM
  friendstbl f
WHERE
  fkFriendname = "{{username}}"
  AND ignored = 0
  AND fkUsername != "{{username}}"
  AND fkUsername NOT IN
      (
        SELECT fkFriendname FROM friendstbl WHERE fkUsername = "{{username}}"
      )
LIMIT 1;

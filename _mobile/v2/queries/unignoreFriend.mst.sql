UPDATE
  friendstbl
SET
  ignored = 0
WHERE fkFriendname = "{{username}}"
AND fkUsername = "{{friendUsername}}"

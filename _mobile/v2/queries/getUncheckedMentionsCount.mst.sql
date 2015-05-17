SELECT
  COUNT(*) as count
FROM
  mentionstbl
WHERE
  fkMentionedName = "{{username}}"
AND
  hasBeenViewed = 0

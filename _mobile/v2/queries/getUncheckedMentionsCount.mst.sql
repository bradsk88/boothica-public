SELECT
  COUNT(*) as count
FROM
  mentionstbl
WHERE
  fkMentionedName = "{{username}}"
AND
  isNew = 1

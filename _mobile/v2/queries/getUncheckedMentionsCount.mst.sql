SELECT
  COUNT(*) as count,
  , not m.hasBeenViewed as isNew
FROM
  mentionstbl
WHERE
  fkMentionedName = "{{username}}"
AND
  isNew = 1

UPDATE
  mentionstbl
SET
  hasBeenViewed = 1
WHERE
  fkMentionedName = '{{username}}'

SELECT
  COUNT(*) as count
FROM
  privatemsgtbl
WHERE
  toUsername = "{{username}}"
AND
  isread = 0

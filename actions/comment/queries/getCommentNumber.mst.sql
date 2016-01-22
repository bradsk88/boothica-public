SELECT
  pkCommentNumber
FROM
  commentstbl
WHERE
  fkUsername = "{{username}}"
ORDER BY datetime DESC
LIMIT 1;

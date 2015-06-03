SELECT
  *
FROM
  emailtbl
WHERE
  fkUsername = "{{username}}"
LIMIT 1;
SELECT
  email
FROM
  emailtbl
WHERE fkUsername = "{{username}}"
AND email = "{{email}}"
LIMIT 1;
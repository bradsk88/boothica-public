SELECT
  privacyDescriptor
FROM
  usersprivacytbl
WHERE
  fkUsername = "{{username}}"
LIMIT 2
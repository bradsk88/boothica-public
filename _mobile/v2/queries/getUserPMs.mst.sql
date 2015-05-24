SELECT
  fromUsername as username, message, `datetime`
FROM
  privatemsgtbl
WHERE
  (fromUsername = "{{otherUsername}}" AND toUsername = "{{username}}")
OR
  (toUsername = "{{otherUsername}}" AND fromUsername = "{{username}}")

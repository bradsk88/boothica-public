UPDATE
  privatemsgtbl
SET
  isread = 1
WHERE
  fromUsername = "{{otherUsername}}" AND toUsername = "{{username}}"

INSERT INTO privatemsgtbl
(
  message, fromUsername, toUsername
)
VALUES
(
  {% autoescape off %}
  '{{message}}',
  {% autoescape on %}
  '{{username}}',
  '{{otherUsername}}'
)

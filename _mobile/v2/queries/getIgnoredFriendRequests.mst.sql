SELECT
  fkUsername as username, `datetime`
FROM
  friendstbl f
WHERE
  fkFriendname = "{{username}}"
  AND fkFriendname != fkUsername
  AND ignored = 1
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

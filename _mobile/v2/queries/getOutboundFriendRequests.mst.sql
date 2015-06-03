SELECT
  fkFriendname as username, `datetime`
FROM
  friendstbl f
WHERE
  fkUsername = "{{username}}"
  AND fkFriendname != fkUsername
  AND fkFriendname NOT IN
  (
    SELECT fkUsername FROM friendstbl WHERE fkFriendname = "{{username}}"
  )
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

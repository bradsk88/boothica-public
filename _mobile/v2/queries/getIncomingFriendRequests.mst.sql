SELECT
  fkUsername as username, `datetime`
FROM
  friendstbl f
WHERE
  fkFriendname = "{{username}}"
  AND fkFriendname != fkUsername
  AND ignored = 0
  AND fkUsername NOT IN
  (
    SELECT fkFriendname FROM friendstbl WHERE fkUsername = "{{username}}"
  )
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

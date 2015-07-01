SELECT
  b.fkUsername
  , b.pkNumber
  , b.blurb
  , b.datetime
  , b.imageTitle
  , b.filetype
  , b.imageHeightProp
FROM boothnumbers b
WHERE b.fkUsername IN (
  SELECT fkFriendname FROM friendstbl
  WHERE lower(fkUsername) = '{{username}}'
)
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

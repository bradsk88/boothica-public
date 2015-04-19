SELECT
  fkUsername
FROM
  likes_boothstbl
WHERE
  fkBoothNumber = {{boothNumber}}
AND
  value = 1
ORDER BY datetime DESC
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

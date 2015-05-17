SELECT
  m.fkMentionerName as mentioner
  , b.pkNumber as boothnumber
  , b.imageTitle as imageTitle
  , b.filetype as filetype
  , b.fkUsername as boother
  , c.commentBody as text
  , not m.hasBeenViewed as isNew
FROM
  mentionstbl m
INNER JOIN
  boothnumbers b
ON
  m.fkBoothNumber = b.pkNumber
INNER JOIN
  commentstbl c
ON
  m.fkIndex = c.pkCommentNumber
WHERE
  m.fkMentionedName = "{{username}}"
ORDER BY m.fkIndex DESC
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 9
{% endif %}

SELECT
    booth.fkUsername as fkUsername
  , booth.pkNumber as pkNumber
  , booth.blurb as blurb
  , booth.imageTitle as imageTitle
  , booth.filetype as filetype
  , datetime
FROM
  boothnumbers booth
WHERE
(
  booth.fkUsername
  IN (
    SELECT fkUsername
    FROM usersprivacytbl
    WHERE fkUsername = booth.fkUsername
    AND privacyDescriptor = 'public'
  )
)
{% if lowerBound %}
AND
(
  pkNumber > {{lowerBound}}
)
{% endif %}
{% if upperBound %}
AND
(
  pkNumber < {{upperBound}}
)
{% endif %}
ORDER BY booth.datetime DESC
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

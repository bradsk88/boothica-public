SELECT
  booth.fkUsername as fkUsername
, booth.pkNumber as pkNumber
, booth.blurb as blurb
, booth.imageTitle as imageTitle
, booth.filetype as filetype
, booth.datetime
FROM boothnumbers AS booth
WHERE
  (
    SELECT TRUE
    FROM usersprivacytbl privacy
    WHERE privacy.fkUsername = booth.fkUsername
          AND (
      privacy.privacyDescriptor = 'public'
      OR
      privacy.privacyDescriptor = 'semi-public'
    )
  )
  AND booth.fkUsername NOT IN (
    SELECT fkFriendName FROM friendstbl
    WHERE fkUsername = '{{username}}'
  )
ORDER BY datetime DESC
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

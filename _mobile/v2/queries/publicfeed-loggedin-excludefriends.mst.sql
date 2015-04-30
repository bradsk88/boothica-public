SELECT DISTINCT * FROM (

  SELECT
    booth.fkUsername as fkUsername
  , booth.pkNumber as pkNumber
  , booth.blurb as blurb
  , booth.imageTitle as imageTitle
  , booth.filetype as filetype
  , booth.datetime
  FROM boothnumbers AS booth

  LEFT JOIN friendstbl AS friends
  ON
    booth.fkUsername = friends.fkUsername

  LEFT JOIN usersprivacytbl AS privacy
  ON
    booth.fkUsername = privacy.fkUsername

  WHERE friends.fkUsername != '{{username}}'
  AND friends.fkFriendname != '{{username}}'
  AND (
    privacy.privacyDescriptor = 'public'
    OR privacy.privacyDescriptor = 'semi-public'
  )

) booths

ORDER BY datetime DESC
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

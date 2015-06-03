SELECT * FROM (

  SELECT
    booth.fkUsername as fkUsername
  , booth.pkNumber as pkNumber
  , booth.blurb as blurb
  , booth.imageTitle as imageTitle
  , booth.filetype as filetype
  , booth.datetime
  FROM boothnumbers AS booth
  LEFT JOIN usersprivacytbl AS privacy
  ON booth.fkUsername = privacy.fkUsername
  WHERE privacy.privacyDescriptor = 'public'
  OR privacy.privacyDescriptor = 'semi-public'

  UNION

  SELECT
      booth.fkUsername, booth.pkNumber, datetime
  FROM
      boothnumbers AS booth
  LEFT JOIN friendstbl AS friend
    ON booth.fkUsername = friend.fkUsername
  LEFT JOIN usersprivacytbl AS privacy
    ON booth.fkUsername = privacy.fkUsername
  WHERE privacy.privacyDescriptor = 'private'
  AND friend.fkFriendName = 'roze'

) booths
ORDER BY datetime DESC
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

SELECT
  username , lastonline, lastonline > NOW( ) - INTERVAL 5 MINUTE AS online
FROM
  logintbl l
WHERE
  username IN (
    SELECT
      fkFriendName
    FROM
      friendstbl
    WHERE
      fkUsername = '{{bootherName}}'
      AND fkFriendName IN (
        SELECT `fkUsername` FROM `friendstbl`
        WHERE `fkFriendName` = '{{bootherName}}'
      )
  )
ORDER BY
  lastonline DESC
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

SELECT COUNT(*) as count, `datetime`, name FROM (
    SELECT
      `dateTime`, fromUsername as name
    FROM
      `privatemsgtbl`
    WHERE
      `toUsername` = "{{username}}"
    UNION
    SELECT
      `dateTime`, toUsername as name
    FROM
      privatemsgtbl
    WHERE fromUsername = "{{username}}"
    ORDER BY datetime DESC
) AS `X`
GROUP BY name
ORDER BY `datetime` DESC
{% if limitsGiven %}
LIMIT {{startIndex}}, {{numPerPage}}
{% else %}
LIMIT 0, 5
{% endif %}

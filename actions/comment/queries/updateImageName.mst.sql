UPDATE
  commentstbl
SET
  hash = "{{imageTitle}}",
  hasPhoto = {% if hasMedia %} 1 {% else %} 0 {% endif %}
WHERE
  pkCommentNumber = {{commentNumber}};

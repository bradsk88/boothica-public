INSERT INTO
  boothcommentrangetbl
(
  fkBoothNumber, fkOldestComment, fkNewestComment
)
VALUES
(
  {{boothNumber}}, {{commentNumber}}, {{commentNumber}}
)
ON DUPLICATE KEY UPDATE fkNewestComment = {{commentNumber}};

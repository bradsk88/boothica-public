INSERT INTO
  commentstbl
(
  fkNumber, fkUsername, commentBody, extension
)
VALUES
(
  {{boothNumber}}, "{{username}}", "{%autoescape off%}{{comment}}{%autoescape on%}", "{{fileExtension}}"
);

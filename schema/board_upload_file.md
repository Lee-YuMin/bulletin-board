<board_upload_file> 게시판 업로드 파일

file_uuid       CHAR(36)     NOT NULL
file_name       VARCHAR(100)    NOT NULL
board_sequence  INT(10)         UNSIGNED NOT NULL
created_at      date            NOT NULL
updated_at      date            NOT NULL
PRIMARY KEY (file_uuid)

ENGINE=InnoDB / DEFAULT CHARSET=utf8

<INDEX>
 - file_uuid

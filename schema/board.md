<board> 게시판

sequence    INT(10)         UNSIGNED NOT NULL AUTO_INCREMENT
id          VARCHAR(50)     NOT NULL
email       VARCHAR(50)
password    VARCHAR(50)     NOT NULL
title       VARCHAR(100)    NOT NULL
content     VARCHAR(2000)
ip_add      VARCHAR(39)
view_count  INT(10)         UNSIGNED NOT NULL DEFAULT '0'
re_order    INT(10)         UNSIGNED NOT NULL
re_depth    INT(10)         UNSIGNED NOT NULL DEFAULT '0'
re_group    INT(10)         UNSIGNED NOT NULL DEFAULT '0'
created_at  date            NOT NULL
updated_at  date            NOT NULL
PRIMARY KEY (sequence)

ENGINE=InnoDB / DEFAULT CHARSET=utf8

<INDEX>
 - sequence
 - id
 - title
 - content
 - title + content
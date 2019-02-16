CREATE TABLE board (
seqeunce    INT(10)         UNSIGNED NOT NULL AUTO_INCREMENT,
id          VARCHAR(50)     NOT NULL,
email       VARCHAR(50),
password    VARCHAR(50)     NOT NULL,
title       VARCHAR(100)    NOT NULL,
content     VARCHAR(2000),
ip_add      VARCHAR(39)     ,
view_count  INT(10)         UNSIGNED NOT NULL DEFAULT '0',
re_group    INT(10)         UNSIGNED NOT NULL,
re_depth    INT(10)         UNSIGNED NOT NULL DEFAULT '0',
parent      INT(10)         UNSIGNED NOT NULL DEFAULT '0',
created_at  datetime        NOT NULL,
updated_at  datetime        NOT NULL,
PRIMARY KEY (seqeunce)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

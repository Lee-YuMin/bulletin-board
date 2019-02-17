DB : MySQL 8.0

Server : PHP 7.2
(API 제공 방식)

Client : Polymer 1.0
(AJAX 호출)

WEB Server : Nginx

<게시판 조회>
Request Value : paging_num, (+검색 조건)
Response Value : sequence, id, title, view_count, re_group, re_depth, parent, created_at
Output : 번호 / 제목 / 작성자 / 작성일 / 조회수
기타 : 10개 단위로 페이징

<게시글 추가 요청>
Request Value : id, email, password, title, content, ip_add, re_group, re_depth, parent, created_at, updated_at, [file_list]
Response Value : status
Output : 성공 or 실패

<게시글 삭제 요청>
Request Value : sequence
Response Value : status
Output : 성공 or 실패

<게시글 수정 화면>
Request Value : sequence
Response Value : sequence, id, email, title, content, [file_list]
Output : 수정 화면

<게시글 수정 요청>
Request Value : sequence, id, email, password, title, content, file_name, created_at, updated_at, [file_list]
Response Value : status
Output : 성공 or 실패

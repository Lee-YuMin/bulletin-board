< 개발 환경 >
- DB : MySQL 8.0
- Server : PHP 7.2
- Client : Polymer 1.0
- WEB Server : Nginx


< 화면 별 기능 리스트 >
1. 게시글 생성 화면
 - 게시글 생성 요청
 - 폼 입력 초기화
 - 뒤로 가기

2. 게시글 리스트 조회 화면
 - 게시글 리스트 요청
 - 페이징
 - 조건 별 검색
 - 게시글 클릭 시 상세 조회
 - 게시글 생성 화면으로 이동

3. 게시글 상세 조회 화면
 - 게시글 상세 정보 요청
 - 게시글 수정(편집) 모드 전환
 - 게시글 삭제 요청
 - 게시글 답글 달기
 - 뒤로 가기

4. 게시글 수정 화면
 - 게시글 수정 요청
 - 비밀번호 매칭 후 업데이트 처리

5. 게시글 답글 화면
 - 답글 게시글 생성 요청
 - 폼 입력 초기화
 - 뒤로 가기

6. 기타 보안 작업
 - XSS 방지용 htmlspecialchars와 strip_tags적용
 - SQL Injection 방지용 SQL bindValue 적용
 - CSRF 방지용 토큰 적용


< 데이터 Request & Response >
1. 게시판 조회
 - Request Value : paging_num, (+검색 조건)
 - Response Value : sequence, id, title, view_count, re_order, re_depth, re_group, created_at, (+게시글 개수)
 - Output : 번호 / 제목 / 작성자 / 작성일 / 조회수
 - 기타 : 10개 단위로 페이징

2. 게시글 생성 요청
 - Request Value : id, email, password, title, content, ip_add, parentSeq, token
 - Response Value : status
 - Output : 성공 or 실패

3. 게시글 상세 요청
 - Request Value : sequence, token
 - Response Value : id, email, title, content, ip_add, created_at
 - Output : 성공 or 실패

4. 게시글 삭제 요청
 - Request Value : sequence, token
 - Response Value : status
 - Output : 성공 or 실패

5. 게시글 수정 요청
 - Request Value : sequence, email, password, title, content, token
 - Response Value : status
 - Output : 성공 or 실패

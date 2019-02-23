(function (){
    let start = 1
    let end = 147

    for(i=start; i<=end; i++){
        console.log(`INSERT INTO mysite.board(id, email, password, title, content, ip_add, view_count, re_order, re_depth, re_group, created_at, updated_at) VALUES('id-${i}', 'email-${i}', '123', 'title-${i}', 'content-${i}', '127.0.0.1', 0, 0, 0, (SELECT * FROM (SELECT MAX(sequence)+1 as sequence FROM board) a), sysdate(), sysdate());`);
    }
})();


// 디비버 실행 : Alt + x
// 초기 데이터시에는 're_order'을 넣는 필드에서 쿼리를 제거하고 1개를 넣어줘야함.

// 시퀀스 초기화 : alter table board auto_increment=1
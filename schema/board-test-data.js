(function (){
    let start = 66
    let end = 147

    for(i=start; i<=end; i++){
        console.log(`INSERT INTO mysite.board(id, email, password, title, content, file_name, ip_add, view_count, re_group, re_depth, parent, created_at, updated_at) VALUES('id-${i}', 'email-${i}', '123', 'title-${i}', 'content-${i}', "", '127.0.0.1', 0, (SELECT * FROM (SELECT MAX(sequence)+1 as sequence FROM board) a), 0, 0, sysdate(), sysdate());`);
    }
})();


// 디비버 실행 : Alt + x
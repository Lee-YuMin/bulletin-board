<?php
class BulletinBoard {
 
    private $conn;
    private $table_name = "board";

    public $sequence;
    public $id;
    public $email;
    public $password;
    public $title;
    public $content;
    public $ip_add;
    public $view_count;
    public $re_group;
    public $re_depth;
    public $parent;
    public $created_at;
    public $updated_at;

    public $pageNum;
    public $type;
    public $typeContent;

    public function __construct($db){
        $this->conn = $db;
    }

    // 게시글의 리스트 조회
    function read() {
        $SELECT_LIMIT = 10;

        // 몇 번째의 페이지 인지 계산 : (페이지 번호 - 1) * 한 페이지의 총 개수
        $page = ($this->pageNum - 1) * $SELECT_LIMIT;

        $sql = 'SELECT ';
        $sql.= '    sequence, id, title, view_count, DATE_FORMAT(created_at, "%Y-%m-%d") as created_at, re_group, re_depth, parent ';
        $sql.= 'FROM ';
        $sql.=     $this->table_name;
        $sql.=' WHERE 1=1 ';
        $sql.= $this->checkCondition($this->type, $this->typeContent);
        $sql.= 'ORDER BY '; 
        $sql.= '    sequence DESC ';
        $sql.= 'LIMIT :startNum, :pageCount;';

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':startNum', $page, PDO::PARAM_INT);
        $stmt->bindValue(':pageCount', $SELECT_LIMIT, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // 페이징을 위한 총 데이터 개수
    function count() {
        $sql = 'SELECT count(*) as count FROM '.$this->table_name;
        $sql.= ' WHERE 1=1 ';
        $sql.= $this->checkCondition($this->type, $this->typeContent);
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'];
    }

    function create() {
        // "부모의 시퀀스 = 그룹 아이디" 를 위한 [최대 시퀀스+1] 계산
        $re_group = '(SELECT * FROM (SELECT MAX(seqeunce)+1 as seqeunce FROM board) tmp);';

        // 데이터 삽입
        $sql = 'INSERT INTO '.$this->table_name;
        $sql.= '    (id, email, password, title, content, ip_add, view_count, ';
        $sql.= '      re_group, re_depth, parent, created_at, updated_at) ';
        $sql.= 'VALUES ';
        $sql.= '    (:id, :email, :password, :title, :content, :ipAdd, 0, ';
        $sql.= '      :reGroup, 0, 0, sysdate(), sysdate());';
        
        $stmt = $this->conn->prepare($sql);
        
        // XSS 제거
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindValue(':id',       $this->id, PDO::PARAM_STR);
        $stmt->bindValue(':email',    $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $this->password, PDO::PARAM_STR);
        $stmt->bindValue(':title',    $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':content',  $this->content, PDO::PARAM_STR);
        $stmt->bindValue(':ipAdd',    $this->ip_add, PDO::PARAM_STR);
        $stmt->bindValue(':reGroup',  $re_group, PDO::PARAM_INT);

        return $stmt->execute() ? true : false;
    }

    function detail() {
        $sql = 'SELECT ';
        $sql.= '    sequence, id, email, title, content, ip_add, parent, DATE_FORMAT(created_at, "%Y-%m-%d") as created_at ';
        $sql.= 'FROM ';
        $sql.=     $this->table_name;
        $sql.=' WHERE sequence = :sequence;';

        $stmt = $this->conn->prepare($sql);
        $this->sequence = htmlspecialchars(strip_tags($this->sequence));

        $stmt->bindValue(':sequence', $this->sequence, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->sequence = $row['sequence'];
        $this->id = $row['id'];
        $this->email = $row['email'];
        $this->title = $row['title'];
        $this->content = $row['content'];
        $this->ip_add = $row['ip_add'];
        $this->parent = $row['parent'];
        $this->created_at = $row['created_at'];
    }

    function viewCountIncrease() {
        $sql = 'UPDATE ';
        $sql.=      $this->table_name;
        $sql.=' SET ';
        $sql.= '    view_count = view_count + 1';
        $sql.=' WHERE ';
        $sql.= '    sequence = :sequence;';

        $stmt = $this->conn->prepare($sql);
        $this->sequence = htmlspecialchars(strip_tags($this->sequence));
        
        $stmt->bindValue(':sequence', $this->sequence, PDO::PARAM_INT);
        $stmt->execute();
    }

    function delete() {
        $sql = 'DELETE FROM ';
        $sql.=      $this->table_name;
        $sql.=' WHERE ';
        $sql.='     sequence = :sequence';

        $stmt = $this->conn->prepare($sql);
        $this->sequence = htmlspecialchars(strip_tags($this->sequence));

        $stmt->bindValue(':sequence', $this->sequence, PDO::PARAM_INT);
        
        return $stmt->execute() ? true : false;
    }

    function update() {
        $sql = 'UPDATE ';
        $sql.=      $this->table_name;
        $sql.=' SET ';
        $sql.= '    title      = :title,';
        $sql.= '    email      = :email,';
        $sql.= '    content    = :content';
        $sql.= '    updated_at = sysdate()';
        $sql.=' WHERE ';
        $sql.= '    sequence = :sequence AND';
        $sql.= '    password = :password;';

        $stmt = $this->conn->prepare($sql);
        
        $this->sequence = htmlspecialchars(strip_tags($this->sequence));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->content = htmlspecialchars(strip_tags($this->content));
        
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
        $stmt->bindValue(':sequence', $this->sequence, PDO::PARAM_INT);
        $stmt->bindValue(':password', $this->password, PDO::PARAM_STR);

        $stmt->execute();
        
        // 변경된 row의 수
        return $stmt->rowCount();
    }

    // 검색 조건이 있을시 조건 쿼리 String을, 없으면 ''을 리턴
    private function checkCondition($type, $typeContent) {
        if(!isset($typeContent) || $typeContent == '') 
            return '';

        $type = htmlspecialchars(strip_tags($type));
        $typeContent = htmlspecialchars(strip_tags($typeContent));

        if($type == 'title_content')
            $template = 'AND (title like "%$content%" OR content like "%$content%") ';
        else
            $template = 'AND $type like "%$content%" ';
        
        $match = array('$type'=>$type, '$content'=>$typeContent);
        
        return strtr($template, $match);
    }
}
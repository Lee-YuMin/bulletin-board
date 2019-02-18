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

    public function __construct($db){
        $this->conn = $db;
    }

    // 게시글의 리스트 조회
    function read() {
        $SELECT_LIMIT = 10;
        $type = $_GET['type'];
        $content = $_GET['content'];
        $pageNum = $_GET['pageNum'];
        $typeCondition;

        // 검색 조건이 있을시 $typeCondition에 조건을 추가
        if(isset($content)) {
            if($type == 'title_content')
                $template = 'AND (title like "%$content%" OR content like "%$content%") ';
            else
                $template = 'AND $type like "%$content%" ';
            
            $match = array('$type'=>$type, '$content'=>$content);
            $typeCondition = strtr($template, $match);
        }
        
        // 몇 번째의 페이지 인지 계산 : (페이지 번호 - 1) * 한 페이지의 총 개수
        $page = ($pageNum - 1) * $SELECT_LIMIT;

        $sql = 'SELECT ';
        $sql.=     'sequence, id, title, view_count, DATE_FORMAT(created_at, "%Y-%m-%d") as created_at, re_group, re_depth, parent ';
        $sql.= 'FROM ';
        $sql.=     $this->table_name;
        $sql.= ' WHERE 1=1 ';
        if(isset($typeCondition))
        $sql.= $typeCondition;
        $sql.= 'ORDER BY '; 
        $sql.=     'sequence DESC ';
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
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['count'];
    }

    function create() {
        
    }
}
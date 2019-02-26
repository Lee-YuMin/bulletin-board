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
    public $re_order;
    public $re_depth;
    public $re_group;
    public $created_at;
    public $updated_at;

    public $pageNum;
    public $type;
    public $typeContent;
    public $parentSeq;

    public function __construct($db){
        $this->conn = $db;
    }

    // 게시글의 리스트 조회
    function read() {
        $SELECT_LIMIT = 10;

        // 몇 번째의 페이지 인지 계산 : (페이지 번호 - 1) * 한 페이지의 총 개수
        $page = ($this->pageNum - 1) * $SELECT_LIMIT;

        $sql = '';
        $sql.=' SELECT ';
        $sql.='    sequence,';
        $sql.='    id,';
        $sql.='    title,'; 
        $sql.='    view_count, ';
        $sql.='    DATE_FORMAT(created_at, "%Y-%m-%d") as created_at,';
        $sql.='    re_order,';
        $sql.='    re_depth,';
        $sql.='    re_group ';
        $sql.=' FROM ';
        $sql.=     $this->table_name;
        $sql.=' WHERE 1=1 ';
        $sql.=     $this->_checkCondition($this->type, $this->typeContent);
        $sql.=' ORDER BY '; 
        $sql.='    re_group DESC,';
        $sql.='    re_order ASC,';
        $sql.='    sequence DESC';
        $sql.=' LIMIT :startNum, :pageCount;';

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(':startNum', $page, PDO::PARAM_INT);
            $stmt->bindValue(':pageCount', $SELECT_LIMIT, PDO::PARAM_INT);
            $stmt->execute();

            if(!$stmt->execute()) throw new Exception('DB Error');
            
            return $stmt;
            
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('message'=> $e->getMessage()));
            die();
        }
    }

    // 페이징을 위한 총 데이터 개수
    function count() {
        $sql = '';
        $sql.=' SELECT ';
        $sql.='     count(*) as count';
        $sql.=' FROM ';
        $sql.=      $this->table_name;
        $sql.=' WHERE 1=1 ';
        $sql.=      $this->_checkCondition($this->type, $this->typeContent);
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'];
    }

    // 게시글 생성
    function create() {
        // parentSeq이 없다면 원글
        if(is_null($this->parentSeq)) {
            $this->re_group = $this->_getNextSequence();
            $this->re_order = 0;
            $this->re_depth = 0;
        } else {
            $parent = $this->_getParent($this->parentSeq);

            $this->re_group = $parent['re_group'];
            $this->re_order = $parent['re_order'] + 1;
            $this->re_depth = $parent['re_depth'] + 1;

            $this->_shiftSibling($parent);
        }
        
        // 데이터 삽입
        $sql = '';
        $sql.=' INSERT INTO '.$this->table_name;
        $sql.='     (id, email, password, title, content, ip_add, view_count, ';
        $sql.='       re_order, re_depth, re_group, created_at, updated_at) ';
        $sql.=' VALUES ';
        $sql.='     (:id, :email, :password, :title, :content, :ip_add, 0, ';
        $sql.='       :re_order, :re_depth, :reGroup, sysdate(), sysdate());';
        
        try {
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
            $stmt->bindValue(':ip_add',   $this->ip_add, PDO::PARAM_STR);
            $stmt->bindValue(':reGroup',  $this->re_group, PDO::PARAM_INT);
            $stmt->bindValue(':re_order', $this->re_order, PDO::PARAM_INT);
            $stmt->bindValue(':re_depth', $this->re_depth, PDO::PARAM_INT);
            
            $result =  $stmt->execute();

            if(!$result)
                throw new Exception('DB Error');
            else if($stmt->rowCount() == 0)
                throw new Exception('Failed Create.');

            return $result;

        } catch(Exception $e) {
            http_response_code(404);
            echo json_encode(array('message'=> $e->getMessage()));
            die();
        }
    }

    function detail() {
        $sql = '';
        $sql.=' SELECT ';
        $sql.='     sequence,';
        $sql.='     id,';
        $sql.='     email,';
        $sql.='     title,';
        $sql.='     content,';
        $sql.='     ip_add,';
        $sql.='     re_group,';
        $sql.='     re_order,';
        $sql.='     re_depth,';
        $sql.='     DATE_FORMAT(created_at, "%Y-%m-%d") as created_at';
        $sql.=' FROM ';
        $sql.=      $this->table_name;
        $sql.=' WHERE ';
        $sql.='     sequence = :sequence;';

        try {
            $stmt = $this->conn->prepare($sql);
            $this->sequence = htmlspecialchars(strip_tags($this->sequence));
    
            $stmt->bindValue(':sequence', $this->sequence, PDO::PARAM_INT);
            $result = $stmt->execute();  

            if(!$result) 
                throw new Exception('DB Error');
            else if($stmt->rowCount() == 0)
                throw new Exception('This page does not exist.');

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $this->sequence = $row['sequence'];
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->ip_add = $row['ip_add'];
            $this->re_group = $row['re_group'];
            $this->re_order = $row['re_order'];
            $this->re_depth = $row['re_depth'];
            $this->created_at = $row['created_at'];

        } catch(Exception $e) {
            http_response_code(404);
            echo json_encode(array('message'=> $e->getMessage()));
            die();
        }
    }

    function viewCountIncrease() {
        $sql = '';
        $sql.=' UPDATE ';
        $sql.=      $this->table_name;
        $sql.=' SET ';
        $sql.='     view_count = view_count + 1';
        $sql.=' WHERE ';
        $sql.='     sequence = :sequence;';

        $stmt = $this->conn->prepare($sql);
        $this->sequence = htmlspecialchars(strip_tags($this->sequence));
        
        $stmt->bindValue(':sequence', $this->sequence, PDO::PARAM_INT);
        $stmt->execute();
    }

    function delete() {
        
        $this->re_group = htmlspecialchars(strip_tags($this->re_group));
        $this->re_order = htmlspecialchars(strip_tags($this->re_order));
        $this->re_depth = htmlspecialchars(strip_tags($this->re_depth));

        $sql = '';
        $nextSiblingOrder = null;
        
        // 부모 글일 경우엔 그룹을 전체 삭제
        if($this->re_order == 0) {
            $sql.=' DELETE FROM ';
            $sql.=      $this->table_name;
            $sql.=' WHERE ';
            $sql.='     re_group = :re_group;';
        } else {
            // 답글일 경우엔 다음 게시글까지 삭제, 다음 게시글이 없으면 자신의 뒤로 전부 삭제
            $nextSiblingOrder = $this->_getNextSiblingOrder($this->re_group, $this->re_depth, $this->re_order);

            if(!is_null($nextSiblingOrder))
                $condition = 're_order >= :my_order AND re_order < :sibling_order';
            else
                $condition = 're_order >= :my_order';
            
            $sql.=' DELETE FROM ';
            $sql.=      $this->table_name;
            $sql.=' WHERE ';
            $sql.='     re_group = :re_group AND ';
            $sql.=      $condition;
        }

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(':re_group', $this->re_group, PDO::PARAM_INT);
            if($this->re_order != 0)        $stmt->bindValue(':my_order', $this->re_order, PDO::PARAM_INT);
            if(!is_null($nextSiblingOrder)) $stmt->bindValue(':sibling_order', $nextSiblingOrder, PDO::PARAM_INT);
            
            $result = $stmt->execute();
    
            if(!$result)
                throw new Exception('DB Error');
            if($stmt->rowCount() == 0)
                throw new Exception('Delete failed.');
    
            return $result;

        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('message'=> $e->getMessage()));
            die();
        }
    }

    function update() {

            $sql = '';
            $sql.=' UPDATE ';
            $sql.=      $this->table_name;
            $sql.=' SET ';
            $sql.='     title      = :title,';
            $sql.='     email      = :email,';
            $sql.='     content    = :content,';
            $sql.='     updated_at = sysdate()';
            $sql.=' WHERE ';
            $sql.='     sequence = :sequence AND';
            $sql.='     password = :password;';

        try{
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

            $result = $stmt->execute();

            if(!$result) 
                throw new Exception('DB Error');
            else if($stmt->rowCount() == 0)
                throw new Exception('Please confirm password.');

            return $result;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('message'=> $e->getMessage()));
            die();
        }
    }

    // 검색 조건이 있을시 조건 쿼리 String을, 없으면 ''을 리턴
    private function _checkCondition($type, $typeContent) {
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

    // 최대 시퀀스 + 1을 구하기 위한 함수
    private function _getNextSequence() {
        $sql = 'SELECT * FROM (SELECT MAX(sequence)+1 as sequence FROM board) a';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['sequence'];
    }

    private function _getParent($parentSeq) {
        $sql = '';
        $sql.=' SELECT';
        $sql.='     sequence, re_group, re_order, re_depth';
        $sql.=' FROM ';
        $sql.=      $this->table_name;
        $sql.=' WHERE';
        $sql.='     sequence = :sequence';

        $stmt = $this->conn->prepare($sql);
        $parentSeq = htmlspecialchars(strip_tags($parentSeq));
        
        $stmt->bindValue(':sequence', $parentSeq, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 최신 답글이 위로 올라오기 위해 같은 그룹들의 글들을 뒤로 한칸 씩 쉬프트
    private function _shiftSibling($parent) {
        $sql = '';
        $sql.=' UPDATE ';
        $sql.=      $this->table_name;
        $sql.=' SET ';
        $sql.='     re_order = re_order + 1';
        $sql.=' WHERE';
        $sql.='     re_group = :re_group AND';
        $sql.='     re_order > :re_order;';

        $stmt = $this->conn->prepare($sql);

        $parent['re_group'] = htmlspecialchars(strip_tags($parent['re_group']));
        $parent['re_order'] = htmlspecialchars(strip_tags($parent['re_order']));
        
        $stmt->bindValue(':re_group', $parent['re_group'], PDO::PARAM_INT);
        $stmt->bindValue(':re_order', $parent['re_order'], PDO::PARAM_INT);

        $stmt->execute();
    }

    // 같은 깊이의 다음 게시글의 순번을 구함
    private function _getNextSiblingOrder($re_group, $re_depth, $re_order) {
        $sql = '';
        $sql.=' SELECT ';
        $sql.='     min(re_order) as re_order ';
        $sql.=' FROM ';
        $sql.=      $this->table_name;
        $sql.=' WHERE ';
        $sql.='     re_group = :re_group AND';
        $sql.='     re_depth = :re_depth AND';
        $sql.='     re_order > :re_order;';
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':re_group', $re_group, PDO::PARAM_INT);
        $stmt->bindValue(':re_order', $re_order, PDO::PARAM_INT);
        $stmt->bindValue(':re_depth', $re_depth, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['re_order'];
    }
}
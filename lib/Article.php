<?php
/**
* 文章
*/
require_once __DIR__.'/ErrorCode.php';

class Article {

	/**
	 * 数据库句柄
	 */
	private $_db;

	/**
	 * 构造方法
	 * @param PDO $_db 数据库连接句柄
	 */
	public function __construct($_db) {
		$this->_db = $_db;
	}

	/**
	 * 添加文章
	 * @param  $title   文章标题
	 * @param  $content 内容
	 * @param  $userId  用户ID
	 * @return bool
	 */
	public function create($title, $content, $userId) {
		if(empty($title)){
			throw new Exception("文章标题不能为空", ErrorCode::ARTICLE_TITLE_CANNOT_EMPTY);
		}
		if(empty($content)){
			throw new Exception("文章内容不能为空", ErrorCode::ARTICLE_CONTENT_CANNOT_EMPTY);			
		}
		$sql = "INSERT INTO `article` (`title`,`content`,`create_at`,`user_id`) VALUES (:title,:content,:createAt,:userId)";
		$stmt = $this->_db->prepare($sql);
		$createAt = time();
		$stmt->bindParam(":title",$title);
		$stmt->bindParam(":content",$content);
		$stmt->bindParam(":createAt",$createAt);
		$stmt->bindParam(":userId",$userId);
		if(!$stmt->execute()){
			throw new Exception("文章添加失败", ErrorCode::ARTICLE_CREATE_FAIL);			
		}
		return array(
			'articleId' => $this->_db->lastInsertId(),
			'title' => $title,
			'content' => $content,
			'userId' => $userId,
			'createAt' => $createAt,
		);
	}

	public function view($articleId) {
		if(empty($articleId)){
			throw new Exception("文章ID不能为空", ErrorCode::ARTICLEID_CANNOT_EMPTY);			
		}
		$sql = "SELECT * FROM `article` WHERE `article_id`=:articleId";
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(":articleId",$articleId);
		$stmt->execute();
		$article = $stmt->fetch(PDO::FETCH_ASSOC);
		if(empty($article)){
			throw new Exception("文章不存在", ErrorCode::ARTICLE_NOT_FOUND);			
		}
		return $article;
	}

	/**
	 * 编辑文章
	 * @param  $articleId 
	 * @param  $title     
	 * @param  $content   
	 * @param  $userId    
	 * @return [type]            
	 */
	public function edit($articleId, $title, $content, $userId) {
		$article = $this->view($articleId);
		if($article['user_id']!==$userId){
			throw new Exception("您无权操作", ErrorCode::PERMISSION_DENIED);
		}
		$title = empty($title)?$article['title']:$title;
		$content = empty($content)?$article['content']:$content;
		if($title === $article['title'] && $content === $article['content'] ){
			return $article;
		}
		$sql = "UPDATE `article` SET `title`=:title,`content`=:content WHERE `article_id`=:articleId and `user_id`=:userId";
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(":title",$title);
		$stmt->bindParam(":content",$content);	
		$stmt->bindParam(":articleId",$articleId);
		$stmt->bindParam(":userId",$userId);				
		if(!$stmt->execute()){
			throw new Exception("文章编辑失败", ErrorCode::ARTICLE_UPDATE_FAIL);			
		}
		return array(
			'articleId' => $articleId,
			'title' => $title,
			'content' => $content,
			'userId' => $userId,
			'createAt' => $article['create_at'],
		);
	}

    /**
     * 删除文章
     * @param  $articleId
     * @param  $userId
     * @return bool
     * @throws Exception
     */
	public function delete($articleId, $userId) {
		$article = $this->view($articleId);
		if($article['user_id'] !== $userId){
			throw new Exception("您无权操作", ErrorCode::PERMISSION_DENIED);
		}	
		$sql = "DELETE FROM `article` WHERE `article_id`=:articleId and `user_id`=:userId";
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(":articleId",$articleId);
		$stmt->bindParam(":userId",$userId);				
		if(!$stmt->execute()){
			throw new Exception("文章删除失败", ErrorCode::ARTICLE_DELETE_FAIL);			
		}	
		return true;		
	}

    /**
     * 获取文章列表
     * @param $userId
     * @param int $page
     * @param int $size
     * @return array
     * @throws Exception
     */
	public function getList($userId, $page=1, $size=10) {
		if($size > 100){
			throw new Exception("分页大小最大为100", ErrorCode::PAGE_SIZE_TOO_BIG);			
		}
		$sql = 'SELECT * FROM `article` WHERE `user_id`=:userId LIMIT :limit,:offset';
		$limit = ($page-1)*$size;
		$limit = $limit < 0 ? 0 : $limit;
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':userId',$userId);
		$stmt->bindParam(':limit',$limit,PDO::PARAM_INT);
		$stmt->bindParam(':offset',$size,PDO::PARAM_INT);
		$stmt->execute();
		$articleList = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $articleList;
	}	
}
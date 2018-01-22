<?php
/**
* API
*/
require __DIR__.'/../lib/User.php';
require __DIR__.'/../lib/Article.php';
$pdo = require __DIR__.'/../lib/db.php';
require_once __DIR__.'/../lib/ErrorCode.php';

class Restful{
	
	/**
	 * @var User
	 */
	private $_user;
	/**
	 * @var Article
	 */
	private $_article;

	/**
	 * 请求方法
	 * @var string
	 */
	private $_requestMethod;
	/**
	 * 请求资源的名称
	 * @var string
	 */
	private $_resourceName;
	/**
	 * 请求资源的ID
	 * @var int
	 */
	private $_id;

	/**
	 * 允许请求的资源列表
	 * @var array
	 */
	private $_allowResources = ['users','articles'];

	/**
	 * 允许请求的方法
	 * @var array
	 */
	private $_allowRequestMethod = ['GET','POST','PUT', 'DELETE','OPTIONS'];

	/**
	 * 常见状态码
	 * @var array
	 */
	private $_statusCode = [
		200 => 'OK',  
		204 => 'No Content',  
		400 => 'Bad Request',  
		401 => 'Unauthorized',  
		402 => 'Payment Required',  
		403 => 'Forbidden',  
		404 => 'Not Found',  
		405 => 'Method Not Allowed',  
		500 => 'Internal Server Error',  
	];

	/**
	 * 构造方法
	 * @param User    $_user    
	 * @param Article $_article 
	 */
	public function __construct(User $_user,Article $_article) {
		$this->_user = $_user;
		$this->_article = $_article;
	}

	/**
	 * 执行
	 */
	public function run() {
		try {
			$this->_setUpRequestMethod();
			$this->_setUpResource();
			if($this->_resourceName=='users'){
				$this->_json($this->_handleUser());
			}else{
                $this->_json($this->_handleArticle());
			}			
		} catch (Exception $e) {
			$this->_json(['error'=>$e->getMessage()], $e->getCode());
		}


	}

	/**
	 * 初始化请求方法
	 */
	private function _setUpRequestMethod(){
		$this->_requestMethod = $_SERVER['REQUEST_METHOD'];
		if(!in_array($this->_requestMethod,$this->_allowRequestMethod)){
			throw new Exception("请求方法不被允许", 405);			
		}
	}

	/**
	 * 初始化资源名称
	 */
	private function _setUpResource(){
		$path = $_SERVER['PATH_INFO'];
		$params = explode('/', $path);
		$this->_resourceName = $params[1];
		if(!in_array($this->_resourceName, $this->_allowResources)){
			throw new Exception("资源请求不被允许", 400);			
		}
		if(!empty($params[2])){
			$this->_id = $params[2];
		}
	}


    /**
     * 输出json
     * @param $array
     * @param int $code
     */
	private function _json($array, $code=0){
	    if($array === null && $code === 0){
	        $code = 204;
        }
        if($array !== null && $code === 0){
            $code = 200;
        }

        header("HTTP/1.1 ".$code." ".$this->_statusCode[$code]);

		header("Content-type:application/json;charset=utf-8");
		if($array !== null){
            echo json_encode($array,JSON_UNESCAPED_UNICODE);
        }

		exit();
	}

	/**
	 * 请求用户
	 * @return bool/array
	 */
	private function _handleUser(){
		if($this->_requestMethod != 'POST'){
			throw new Exception("请求方法不被允许", 405);			
		}
		$body = $this->_getBodyParams();
		if(empty($body['username'])){
			throw new Exception("用户名不能为空", 400);			
		}
		if(empty($body['password'])){
			throw new Exception("密码不能为空", 400);			
		}
		// $data = $this->_user->register($body['username'], $body['password']);
		// var_dump($data);
		// exit;
		return $this->_user->register($body['username'], $body['password']);
	}

	/**
	 * 请求文章
	 * @return bool/array
	 */
	private function _handleArticle(){
		switch ($this->_requestMethod) {
			case 'POST':
				return $this->_handleArticleCreate();
				break;
			case 'PUT':
				return $this->_handleArticleEdit();
				break;
			case 'DELETE':
				return $this->_handleArticleDelete();
				break;		
			case 'GET':
				if(empty($this->_id)){
					return $this->_handleArticleList();
				}else{
					return $this->_handleArticleView();
				}		
				break;										
			default:
				throw new Exception("请求方式不被允许", 405);				
		}
	}

    /**
     * 创建文章
     * @return array
     * @throws Exception
     */
	private function _handleArticleCreate(){
		$body = $this->_getBodyParams();
		if(empty($body['title'])){
			throw new Exception("标题不能为空", 400);			
		}
		if(empty($body['content'])){
			throw new Exception("内容不能为空", 400);			
		}
		$user = $this->_userLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        try{
            $article = $this->_article->create($body['title'],$body['content'],$user['user_id']);
            return $article;
        }catch (Exception $e){
            if(in_array($e->getCode(),[
                ErrorCode::ARTICLE_TITLE_CANNOT_EMPTY,
                ErrorCode::ARTICLE_CONTENT_CANNOT_EMPTY
            ])){
                throw new Exception($e->getMessage(),400);
            }
            throw new Exception($e->getMessage(),500);
        }

	}

    /**
     * 编辑文章
     */
	private function _handleArticleEdit(){
	    $user = $this->_userLogin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        try{
            $article = $this->_article->view($this->_id);
            //var_dump($user['user_id']);exit;
            if($article['user_id'] !== $user['user_id']){
                throw new Exception('您无权编辑',403);
            }
            $body = $this->_getBodyParams();
            $title = empty($body['title'])?$article['title']:$body['title'];
            $content = empty($body['content'])?$article['content']:$body['content'];
            if($title === $article['title'] && $content === $article['content']){
                return $article;
            }else{
                $article = $this->_article->edit($article['article_id'],$title,$content,$user['user_id']);
                return $article;
            }

        } catch (Exception $e){
            if($e->getCode() < 100){
                if($e->getCode() == ErrorCode::ARTICLE_NOT_FOUND){
                    throw new Exception($e->getMessage(),404);
                }else {
                    throw new Exception($e->getMessage(),400);
                }
            }else{
                throw $e;
            }
        }

	}

    /**
     * 删除文章
     */
	private function _handleArticleDelete(){
        $user = $this->_userLogin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        try{
            $article = $this->_article->view($this->_id);
            if($article['user_id'] !== $user['user_id']){
                throw new Exception('您无权删除',403);
            }
            $this->_article->delete($article['article_id'],$user['user_id']);
            return null;
        } catch (Exception $e){
            if($e->getCode() < 100){
                if($e->getCode() == ErrorCode::ARTICLE_NOT_FOUND){
                    throw new Exception($e->getMessage(),404);
                }else {
                    throw new Exception($e->getMessage(),400);
                }
            }else{
                throw $e;
            }
        }
	}

    /**
     * 获取文章列表
     */
	private function _handleArticleList(){
        $user = $this->_userLogin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
	    $page = isset($_GET['page'])?intval($_GET['page']):1;
        $size = isset($_GET['size'])?intval($_GET['size']):10;
        //var_dump($size);
        try{
            return $this->_article->getList($user['user_id'], $page, $size);
        }catch (Exception $e){
            throw new Exception($e->getMessage(),400);
        }

	}

    /**
     * 查看文章
     */
	private function _handleArticleView(){
        try{
            return $this->_article->view($this->_id);
        }catch (Exception $e){
            if($e->getCode() == ErrorCode::ARTICLE_NOT_FOUND){
                throw new Exception($e->getMessage(),404);
            }else {
                throw new Exception($e->getMessage(),500);
            }
        }
	}

    /**
     * 用户登录
     * @param $PHP_AUTH_USER
     * @param $PHP_AUTH_PW
     * @return array
     * @throws Exception
     */
	private function _userLogin($PHP_AUTH_USER,$PHP_AUTH_PW){
        try{
            return $this->_user->login($PHP_AUTH_USER, $PHP_AUTH_PW);
        } catch (Exception $e){
            if(in_array($e->getCode(),[
                ErrorCode::USERNAME_CANNOT_EMPTY,
                ErrorCode::PASSWORD_CANNOT_EMPTY,
                ErrorCode::USERNAME_OR_PASSWORD_INVALID
            ])){
                throw new Exception($e->getMessage(),400);
            }
            throw new Exception($e->getMessage(),500);
        }
	}

    /**
     * 获取参数
     * @return array
     * @throws Exception
     */
	private function _getBodyParams(){
		$raw = file_get_contents("php://input");
		if(empty($raw)){
			throw new Exception("请求参数错误", 400);			
		}
	
		return json_decode($raw,true);
	}
}

$user = new User($pdo);
$article = new Article($pdo);

$restful = new Restful($user, $article);
$restful->run();
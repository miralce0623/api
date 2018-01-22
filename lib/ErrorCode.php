<?php
/**
* 错误代码
*/
class ErrorCode {
	
	const USERNAME_EXSITS = 1;	//用户名已存在
	const USERNAME_CANNOT_EMPTY = 2; //用户名不能为空
	const PASSWORD_CANNOT_EMPTY = 3; //密码不能为空
	const REGISTER_FAIL = 4; //注册失败
	const USERNAME_OR_PASSWORD_INVALID = 5; //用户名或密码错误
	const ARTICLE_TITLE_CANNOT_EMPTY = 6; //文章标题不能为空
	const ARTICLE_CONTENT_CANNOT_EMPTY = 7; //文章内容不能为空
	const ARTICLE_CREATE_FAIL = 8; //文章添加失败
	const ARTICLEID_CANNOT_EMPTY = 9; //文章ID不能为空
	const PERMISSION_DENIED = 10; //无权编辑
	const ARTICLE_UPDATE_FAIL = 11; //文章编辑失败
	const ARTICLE_NOT_FOUND = 12; //文章不存在
	const ARTICLE_DELETE_FAIL = 13; //文章删除失败
	const PAGE_SIZE_TOO_BIG = 14; //分页大小太大
    const SERVER_INTERNAL_ERROR = 15; //服务器内部错误
}
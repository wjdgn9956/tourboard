<?php

session_start();

include __DIR__ . "/../vendor/autoload.php"; // 컴포저로 설치한 외부 모듈 자동 추가

/* filp/whoops S */
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
/* filp/whoops E */

include "funcs.php"; // 공통함수 
include "app.php"; // 공통 클래스 App
include "autoload.php"; // Component, Controller 파일 자동 추가 

$request = App::load(\Component\Core\Request::class);

App::loginSession(); // 로그인 세션 처리 
App::routes(); 
/**
 라우터 
 사용자가 입력한 주소의 파턴에 따라서 
 적절한 Controller로 연결(객체 생성)
*/
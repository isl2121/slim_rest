<?php
    

/*  제작자 : 진승규
*   제작일 : 2019-04-16
*   참고   : https://meetup.toast.com/posts/92
*/


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;

require '../vendor/autoload.php';
require '../src/config.php';
require '../src/models/user.php';
require '../src/models/board.php';


$app = new \Slim\App($settings);
$app->add(new \Adbar\SessionMiddleware($settings['session']));

$container = $app->getContainer();

$capsule = new Capsule;

$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$app->get('/hello/', function($request, $response) {
   return $response->getBody()->write(User::all()->toJson());
   
});


# 유저 생성, 조회->로그인, 수정 | 과제에서 유저 삭제에 대한 요청이 없어 제작 안함
require 'user/index.php';


# 게시물 페이지 조회, 단일 게시물 조회, 게시물 등록, 게시물 수정, 게시물 삭제
require 'board/index.php';

$app->run();
<?php
    

/*  제작자 : 진승규
*   제작일 : 2019-04-16
*   참고   : https://meetup.toast.com/posts/92
*/


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/src/config.php';


$app = new \Slim\App($settings);
$app->add(new \Adbar\SessionMiddleware($settings['session']));


// Fetch DI Container
$container = $app->getContainer();


$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('path/to/templates', [
        'cache' => false
    ]);
    
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};


$capsule = new Capsule;

$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();


$container['db'] = function ($container) use ($capsule){
   return $capsule;
};



# 유저 생성, 조회->로그인, 수정 | 과제에서 유저 삭제에 대한 요청이 없어 제작 안함
require __DIR__ .'/../routes/user_route.php';



# 게시물 페이지 조회, 단일 게시물 조회, 게시물 등록, 게시물 수정, 게시물 삭제
require __DIR__ .'/../routes/board_route.php';

$app->run();
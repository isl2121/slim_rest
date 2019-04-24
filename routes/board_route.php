<?php

use App\Models\Board;
use App\Controllers\BoardController;
use App\Middleware\IsLogin;

$app->group('/board', function () {
    #$this->get('/hello', 'hello_2');																		# 데이터베이스 연결 확인용
    $this->get('/list/{page}',  BoardController::class . ':listBoard')->setName('boards.list');    	      	# 게시판 페이지 조회   : METHOD [GET]  | URL [ api/board/list/{page} ]
    $this->get('/{idx}', BoardController::class . ':selectBoard')->setName('boards.select');              	# 단일 게시물 조회     : METHOD [GET]  | URL [ api/board/{idx} ]
});



# 로그인한 유저만 사용 가능한 URL
$app->group('/board', function () {
    $this->post('', BoardController::class . ':insertBoard');                 		# 게시물 생성          : METHOD [POST] | URL [ api/board/main ]       | @param [ subject, content ]
    $this->put('/{idx}', BoardController::class . ':updateBoard');       	        # 게시물 변경          : METHOD [PUT]  | URL [ api/board/{idx} ]      | @param [ subject, content ]
    $this->delete('/{idx}', BoardController::class . ':deleteBoard');             	# 게시물 삭제          : METHOD [DELTE]| URL [ api/board/{idx} ]
})->add(new IsLogin());

    /*
    # 데이터베이스 연결 확인용
    function hello_2 ($request, $response, $args)
    {
        return $response->getBody()->write(Board::all()->toJson());
    };
    */


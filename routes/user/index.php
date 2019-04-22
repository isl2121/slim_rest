<?php

use App\Models\User;
use App\Controllers\UserController;

$app->group('/user', function () {
    
    #$this->get('/hello', 'hello');         #데이터베이스 검사용
    
    $this->post('', UserController::class . ':insertUser');     # 유저 생성     : METHOD [POST] | URL [ api/user/main ]  | @param [ user_id, name, password ]
    $this->put('', UserController::class . ':updateUser');      # 유저 변경     : METHOD [PUT]  | URL [ api/user/main ]  | @param [ name ]
    $this->post('/login', UserController::class . ':selectUser');   # 유저 조회     : METHOD [POST] | URL [ api/user/login ].| @param [ user_id, password ]
    $this->get('/logout', UserController::class . ':logoutUser')->setName('user.logout');    # 유저 로그아웃 : METHOD [GET]  | URL [ api/user/logout ]

});

    
    
    #데이터베이스 활성화 체크용
    /*
    function hello ($request, $response, $args)
    {
        return $response->getBody()->write(User::all()->toJson());
    };
    */

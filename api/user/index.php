<?php


$app->group('/user', function ($app) {
    
    #$app->get('/hello', 'hello');         #데이터베이스 검사용
    
    $app->post('/main', 'insertUser');     # 유저 생성     : METHOD [POST] | URL [ api/user/main ]  | @param [ user_id, name, password ]
    $app->put('/main', 'updateUser');      # 유저 변경     : METHOD [PUT]  | URL [ api/user/main ]  | @param [ name ]
    $app->post('/login', 'selectUser');    # 유저 조회     : METHOD [POST] | URL [ api/user/login ].| @param [ user_id, password ]
    $app->get('/logout', 'logoutUser');    # 유저 로그아웃 : METHOD [GET]  | URL [ api/user/logout ]

});

    
    
    #데이터베이스 활성화 체크용
    /*
    function hello ($request, $response, $args)
    {
        return $response->getBody()->write(User::all()->toJson());
    };
    */
    
    
    /*  insertUser - 유저 생성페이지
     *  method POST
     *   @param string $user_id
     *   @param string $name
     *   @param string $password
    */
    function insertUser ($request, $response, $args)
    {
        $user_id    = $request->getParam('user_id');
        $name       = $request->getParam('name');
        $password   = $request->getParam('password');

        $id_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $user_id);
        $pw_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);
        
        
        # 아이디와 패스워드 유효성 검사확인
        if (strlen(trim($user_id)) > 0 && strlen(trim($password)) > 0  && $id_check > 0 && $pw_check > 0 ) {
            
            #아이디 검사
            $count_user = User::where('user_id', $user_id)->count();
            if ( $count_user > 0 ) {

                $data['res'] = false;
                $data['msg'] = "존재하는 아이디 입니다.";

            } else {
               $user = new User();
               $user->user_id = $user_id;
               $user->password = $password;
            
               #테스트 환경이라 암호화는 처리 안함
               #$user->password = hash('sha256', $password);
               $user->name = $name;

               $result = $user->save();

               if ( $result == 1 ) { #유저 생성에 성공
                   $data['res'] = true;
                   $data['msg'] = "성공적으로 생성되었습니다.";
                   
                   #rest api 에서는 리소스가 생성되었을때는 201을 return 하는걸 권장
                   return $response->withJson($data, 201);
               } else {
                   $data['res'] = false;
                   $data['msg'] = "유저 생성에 실패하였습니다.";
               }   
            }
        } else {
            $data['res'] = false;
            $data['msg'] = "아이디와 비밀번호를 확인하여 주십시요.";
        }
        
        #유저 생성 실패
        return $response->withJson($data, 200);
    };
    
    
    
    /*   selectUser - 유저 로그인 페이지
     *   method POST
     *   @param string $user_id
     *   @param string $password
    */
    function selectUser ($request, $response, $args)
    {   
        $user_id = $request->getParam('user_id');
        $password = $request->getParam('password');
        #$password = hash('sha256', $password);

        $user_check = User::where(array('user_id'=> $user_id, 'password'=>$password))->first();
        
        if ( $user_check ) {
            #유저 정보 세션에 등록
            $session = new \Adbar\Session;
            $session->set('user', $user_check);
    
            $data['res'] = true;
            $data['msg'] = "로그인 되었습니다.";
        } else {

            $data['res'] = false;
            $data['msg'] = "로그인에 실패하였습니다.";            
        }
        
        return $response->withJson($data, 200);
    };
    
    
    /*   updateUser - 유저 정보 수정 페이지
     *   기본적으로 아이디는 변경하지 않음   
     *   method PUT
     *   @param string $name
     *   @param string $password
    */
    function updateUser ($request, $response, $args)
    {
        $session = new \Adbar\Session;
        $user = $session->get('user');

        if ($user) {
            $update_data['name']       = $request->getParam('name', $user->name);
            $update_data['password']   = $request->getParam('password', '');
            
            
            #이름은 없으면 기존에 이름을 불러와도 괜찮지만 패스워드는 유효성검사를 한번 거쳐야하여 별도 처리
            if ($update_data['password'] != '') {
                $pw_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $update_data['password']);
                
                if ($pw_check > 0) {
                    #$update_data['password'] = hash('sha256', $update_data['password']);
                } else {
                    $data['result'] = false;
                    $data['msg'] = "비밀번호가 올바르지 않습니다.";
                    return $response->withJson($data, 200);
                }
            } else {
                $update_data['password'] = $user->password;
            }
            
            $result = User::where('idx', $user->idx )->update($update_data);

            if ($result == 1) {
                
                #기존 세션에 최신정보를 덮어 씌운다.
                $new_data = User::where('idx', $user->idx )->first();
                $session->set('user', $new_data);
                
                $data['result'] = true;
                $data['msg'] = "변경이 완료되었습니다.";
            } else {
                $data['result'] = false;
                $data['msg'] = '변경에 실패하였습니다.';
            }
            
        } else {
            $data['result'] = false;
            $data['msg'] = "로그인 되지 않았습니다.";
            return $response->withJson($data, 401);
        }

        return $response->withJson($data, 200);
    };
    
    
    /*  logoutUser - 유저 로그아웃
     *  개발중 로그아웃이 필요해 제작
     *  method GET
    */
    function logoutUser ($request, $response, $args)
    {
        $session = new \Adbar\Session;
        $user = $session->get('user');
        
        if ($user) {
            $session->clear();
            $data['result'] = true;
            $data['msg'] = '로그아웃 되었습니다.';
            return $response->withJson($data, 200);
        } else {
            $data['result'] = false;
            $data['msg'] = "로그인 되지 않았습니다.";
            return $response->withJson($data, 401);
        }
    }
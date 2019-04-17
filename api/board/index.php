<?php
$app->group('/board', function ($app) {

    #$app->get('/hello', 'hello_2');

    $app->get('/list/{page}',  'listBoard');            # 게시판 페이지 조회   : METHOD [GET]  | URL [ api/board/list/{page} ]
    $app->get('/{idx}', 'selectBoard');                 # 단일 게시물 조회     : METHOD [GET]  | URL [ api/board/{idx} ]
    $app->post('/main', 'insertBoard');                 # 게시물 생성          : METHOD [POST] | URL [ api/board ]            | @param [ subject, content ]
    $app->put('/{idx}', 'updateBoard');                 # 게시물 변경          : METHOD [PUT]  | URL [ api/board/{idx} ]      | @param [ subject, content ]
    $app->delete('/{idx}', 'deleteBoard');              # 게시물 삭제          : METHOD [DELTE]| URL [ api/board/{idx} ]

});

    /*
    # 데이터베이스 연결 확인용
    function hello_2 ($request, $response, $args)
    {
        return $response->getBody()->write(Board::all()->toJson());
    };
    */


    /*  listBoard - 게시판 조회
    *   method GET
    *   @attr  int {page} [ default 1 ]
    */
    function listBoard ($request, $response, $args)
    {
        $page = $request->getAttribute('page');

        #만약에 문자형을 받는다면 기본값으로 1페이지를 보여준다.
        if ( !is_int($page) ) {
            $page = 1;
        }
        
        #기본적으로 10개씩 조회
        $limit  = 10;

        $offset = (--$page) * $limit;        
        $board_data = Board::offset($offset)->limit($limit)->get();

        if (count($board_data) > 0) {
            $data['res'] = true;
            $data['data'] = $board_data;            
        } else {
            $data['res'] = false;
            $data['msg'] = "데이터 조회에 실패하였습니다.";
        }

        return $response->withJson($data, 200);

    };
    
    /*  selectBoard - 게시물 조회
    *   method GET
    *   @attr int /idx
    */
    function selectBoard ($request, $response, $args)
    {
        $idx = $request->getAttribute('idx');
        $result = Board::where('idx',$idx)->first();
        
        if ( $result ) {
            $data['res'] = true; 
            $data['data'] = $result;
        } else {
            $data['res'] = false;
            $data['msg'] = "데이터 조회에 실패하였습니다.";
        }

        return $response->withJson($data, 200);
    };
    
    /*  insertBoard - 게시물 생성
    *   method POST
    *   @parram str subject
    *   @parram str content
    */
    function insertBoard ($request, $response, $args)
    {
        $session = new \Adbar\Session;
        $user = $session->get('user');
                
        #세션 먼저 검사
        if ( !$user ) {
            $data['res'] = false;
            $data['msg'] = "로그인한 유저만 글쓰기가 가능합니다";
            return $response->withJson($data, 401);
        } else {
            
              $subject = $request->getParam('subject');
              $content = $request->getParam('content');

            #제목과 내용 있는지 검사            
            if (strlen(trim($subject)) > 0 && strlen(trim($content))) {
                $board = new Board();
                $board->user_id = $user->user_id;
                $board->subject = $subject;
                $board->content = $content;
                $result = $board->save();

                if ( $result ) {
                    $data['res'] = true;
                    $data['msg'] = "성공적으로 등록되었습니다.";
                    return $response->withJson($data, 201);
                } else {
                    $data['res'] = false;
                    $data['msg'] = "글 등록에 실패하였습니다.";                    
                }

            } else {
                $data['res'] = false;
                $data['msg'] = "제목과 내용을 확인해주십시요.";
            }

        }
        return $response->withJson($data, 200);
    };
    
    /*  updateBoard - 게시물 변경
    *   method PUT
    *   @parram str subject
    *   @parram str content
    */
    function updateBoard ($request, $response, $args)
    {

        $session = new \Adbar\Session;
        $user = $session->get('user');
        
        #세션 먼저 검사
        if ( !$user ) {
            $data['res'] = false;
            $data['msg'] = "로그인한 유저만 수정이 가능합니다.";
            return $response->withJson($data, 401);           
        } else {
            
            $idx =  $request->getAttribute('idx'); 
            $result = Board::where('idx',$idx)->first();
            
            # 게시물이 존재하는지 먼저 확인
            if ($result == null) {
                $data['res'] = false;
                $data['msg'] = "해당 게시판을 조회할수 없습니다.";
                return $response->withJson($data, 401);
            }
            
            #해당 게시물을 본인이 작성하였는지 확인
            if ($user->user_id == $result->user_id ) {
                
                $update_data['subject'] = $request->getParam('subject');
                $update_data['content'] = $request->getParam('content');
                
                $result = Board::where('idx', $idx )->update($update_data);
                
                if ( $result ) {
                    $data['res'] = true;
                    $data['msg'] = "수정에 성공하였습니다.";
                } else {
                    $data['res'] = false;
                    $data['msg'] = "수정에 실패하였습니다.";
                }
                
                return $response->withJson($data, 200);
                
            } else {
                $data['res'] = false;
                $data['msg'] = "본인만 수정이 가능합니다.";
                return $response->withJson($data, 401);      
            }
        }
    }; 
    
    /*  deleteBoard - 게시물 삭제
    *   method DELETE
    *   @attr int /idx
    */
    
    function deleteBoard ($request, $response, $args)
    {
        $session = new \Adbar\Session;
        $user = $session->get('user');
        
        #세션 먼저 검사
        if ( !$user ) {            
            $data['res'] = false;
            $data['msg'] = "로그인한 유저만 수정이 가능합니다.";
            return $response->withJson($data, 401);
        } else {
            
            $idx = $request->getAttribute('idx');
            $result = Board::where('idx',$idx)->first();      
            
            # 존재하는 게시물인지 검사
            if ($result == null) {
                $data['res'] = false;
                $data['msg'] = "해당 게시판을 조회할수 없습니다.";
                return $response->withJson($data, 401);
            }

            # 본인이 업로드한 게시물인지 검사
            if ($user->user_id == $result->user_id ) {
                $result = Board::where('idx', $idx )->delete();
                if ( $result ) {
                    $data['res'] = true;
                    $data['msg'] = "삭제가 완료되었습니다.";
                } else {
                    $data['res'] = false;
                    $data['msg'] = "삭제에 실패하였습니다.";
                }
                
                return $response->withJson($data, 200);
            } else {
                $data['res'] = false;
                $data['msg'] = "본인만 삭제가 가능합니다.";
                return $response->withJson($data, 401);                
            }    
        }
                    
    }; 
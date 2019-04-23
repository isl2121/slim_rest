<?php
	
	namespace App\Controllers;
	
	use App\Models\Board;
	
	class BoardController extends Controller
	{
		/*  listBoard - 게시판 조회
	    *   method GET
	    *   @attr  int {page} [ default 1 ]
	    */
	    public function listBoard ($request, $response, $args)
	    {
		    
	        $page = $args['page'];
	
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
	
	        return $this->c->response->withJson($result, 200);
	    }
	    
	    
	    /*  selectBoard - 게시물 조회
	    *   method GET
	    *   @attr int /idx
	    */
	    public function selectBoard ($request, $response, $args)
	    {
	        $idx = $args['idx'];

	        $result = $this->select_board($idx);
	
	        return $this->c->response->withJson($result, 200);
	    }
	    
	    
	    /*  insertBoard - 게시물 생성
	    *   method POST
	    *   @parram str subject
	    *   @parram str content
	    */
	    public function insertBoard ($request, $response)
	    {
	        $session = new \Adbar\Session;
	        $user = $session->get('user');
	        
	        $is_login = $this->is_login();    
	        
	        #세션 먼저 검사
	        if ( !$is_login['res'] ) {
	            return $this->c->response->withJson($is_login, 401);
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
	                    return $this->c->response->withJson($data, 201);
	                } else {
	                    $data['res'] = false;
	                    $data['msg'] = "글 등록에 실패하였습니다.";                    
	                }
	
	            } else {
	                $data['res'] = false;
	                $data['msg'] = "제목과 내용을 확인해주십시요.";
	            }
	        }
	        return $this->c->response->withJson($data, 200);
	    }
	    
	    
	    /*  updateBoard - 게시물 변경
	    *   method PUT
	    *   @parram str subject
	    *   @parram str content
	    */
	    public function updateBoard ($request, $response, $args)
	    {
	
	        $session = new \Adbar\Session;
	        $user = $session->get('user');
	        
	        $is_login = $this->is_login();    
	        
	        #세션 먼저 검사
	        if ( !$is_login['res'] ) {
	            return $this->c->response->withJson($is_login, 401);
	        } else {

		        $idx = $args['idx'];
	            $idx_result = $this->select_board($idx);
	         	
	         	# 게시물이 존재하는지 먼저 확인
	            if ($idx_result['res'] != true) {
	                return $this->c->response->withJson($idx_result, 401);
	            }
				
	            #해당 게시물을 본인이 작성하였는지 확인
	            if ($user->user_id == $idx_result['data']->user_id ) {
	                
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
	                
	                return $this->c->response->withJson($data, 200);
	                
	            } else {
	                $data['res'] = false;
	                $data['msg'] = "본인만 수정이 가능합니다.";
	                return $this->c->response->withJson($data, 401);      
	            }
	        }
	    }
	    
	    
	    /*  deleteBoard - 게시물 삭제
	    *   method DELETE
	    *   @attr int /idx
	    */
	    public function deleteBoard ($request, $response, $args)
	    {
	        $session = new \Adbar\Session;
	        $user = $session->get('user');
	        
	        $is_login = $this->is_login();    
	        
	        #세션 먼저 검사
	        if ( !$is_login['res'] ) {
	            return $this->c->response->withJson($is_login, 401);
	        } else {
	            
		        $idx = $args['idx'];
	            $idx_result = $this->select_board($idx);
	         	
	         	# 게시물이 존재하는지 먼저 확인
	            if ($idx_result['res'] != true) {
	                return $this->c->response->withJson($idx_result, 401);
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

	                return $this->c->response->withJson($data, 200);
	            } else {
	                $data['res'] = false;
	                $data['msg'] = "본인만 삭제가 가능합니다.";
	                return $this->c->response->withJson($data, 401);                
	            }    
	        }
	                    
	    }
	}
<?php
	
	namespace App\Controllers;
	
	use Interop\Container\ContainerInterface;
		
	abstract class Controller
	{
		
		protected $c;
		
		public function __construct(ContainerInterface $c)
		{
			$this->c = $c;
		}
	
		
		public function is_login()
		{
			$session = new \Adbar\Session;
	        $user = $session->get('user');
	        
	        #세션 먼저 검사
	        if ( !$user ) {
	            $data['res'] = false;
	            $data['msg'] = "로그인한 유저만 수정이 가능합니다.";
	        } else {
		        $data['res'] = true;
	        }

	        return $data;
		}
		
		public function select_board($idx)
		{
			$result = $this->c->db->table('boards')->where('idx',$idx)->first();
			
			if ( $result ) {
	            $data['res'] = true; 
	            $data['data'] = $result;
	        } else {
	            $data['res'] = false;
	            $data['msg'] = "데이터 조회에 실패하였습니다.";
	        }
	        
	        return $data;
		}
		/*
		protected function render404()
		{
			return $this->c->view->render($response->withStatus(404));
		}
		*/
	}
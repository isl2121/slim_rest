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
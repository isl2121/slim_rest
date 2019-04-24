<?
	namespace App\Middleware;
	
	#use Interop\Container\ContainerInterface;
	
	class IsLogin
	{
		
		public function __invoke($request, $response, $next)
		{
			$session = new \Adbar\Session;
	        $user = $session->get('user');
	        
	        #세션 먼저 검사
	        if ( !$user ) {
	            $data['res'] = false;
	            $data['msg'] = "로그인한 유저만 사용이 가능합니다.";
	            
	            return $response->withJson($data, 201);
	        }
		}

	}
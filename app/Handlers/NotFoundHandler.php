<?php
	namespace App\Handlers;
	
	use Slim\Handlers\AbstractHandler;
	use Psr\Http\Message\ServerRequestInterface;
	use Psr\Http\Message\ResponseInterface;
	
	class NotFoundHandler extends AbstractHandler
	{
		public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
		{
			$ouput = $this->renderNotFoundJson($response);
			return $ouput->withStatus(404);
		}
		
		protected function renderNotFoundJson($response)
		{
			return $response->withJson([
				'res' => false,
				'msg'	=> 'Not Found'
			]);
		}
	}

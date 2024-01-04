<?php
namespace App\Lib\Controller;

use App\Lib\Database\Database;
use App\Lib\Routing\Request;
use App\Lib\Routing\Response;
use App\Lib\Security\CSRF\SessionTokenManager;
use App\Lib\View\View;

abstract class BaseController
{
    /**
     * Send a response
     * @var Response
     */
    protected Response $response;
    /**
     * Receive data from request, either filtered data from $_POST or raw JSON content from php://input 
     * @var Request
     */
    protected Request $request;
    protected SessionTokenManager $sessionTokenManager;
    protected Database $db;
    public function __construct()
    {
        $this->sessionTokenManager = SessionTokenManager::getInstance();
        $this->request = new Request();
        $this->response = new Response();
        $this->db = Database::getInstance();
    }
    protected function redirectToRoute(string $route, array $parameters = [])
    {

    }
    protected function renderView(string $view, array $data = [])
    {
        View::render($view, $data);
    }
    /**
     * Checks if $_POST data received has valid csrf token
     * @return bool
     */
    protected function csrfAuth(): bool
    {
        $data = $this->request->getData();
        if (isset($data['token'])) {

            $token = $data['token'];
            $valid = $this->sessionTokenManager->validateToken($token);

            if ($valid) {
                return true;
            }

            $this->sessionTokenManager->regenerateToken();
            return false;
        }
        return false;
    }

}
?>
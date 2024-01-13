<?php
namespace App\Controller;

use App\Entity\User;
use App\Lib\Controller\BaseController;
use App\Lib\Database\Entity\Entity;

class LogoutController extends BaseController
{
    /**
     * [POST] logout request
     * @return void
     */
    public function logout()
    {
        if (!$this->csrfAuth()) {
            return;
        }
        //clear session
        unset($_SESSION['user']);
        unset($_SESSION['csrf_token']);
        $this->redirectToRoute('/login');
    }
}
<?php
namespace App\Controller;

use App\Entity\EmailActivationCode;
use App\Entity\User;
use App\Lib\Controller\BaseController;
use App\Lib\View\View;

class UserController extends BaseController
{
    /**
     * [GET] /account/activate/{code}
     * No Veryfication needed
     * @return void
     */
    public function activateGET($code)
    {
        $message = 'Failed to activate account, contact administrator.';
        $activation = new EmailActivationCode();
        if ($activation->findOneBy(['activation_code', '=', $code])) {
            $user = $activation->getUser();
            if (!$user->getActivated()) {
                $user->setActivated(true);
                $activated = $user->update();
                if ($activated) {
                    $message = 'Your account is now activacted, <a class="basic-link" href="/login">sign in here</a>';
                }
            } else {
                $message = 'Your account is already activated, <a class="basic-link" href="/login">sign in here</a>';
            }

        }
        $view = new View('User/ActivationPartialView', 'Account - Activation info');
        $view->addStyle('error.css');
        $view->renderPartial(['message' => $message]);
    }
    public function showProfile($userLogin)
    {
        $user = new User();
        $user->findOneBy(['login', '=', $userLogin]);
        if ($user->exists()) {
            $data['user'] = $user;
            $view = new View('User/UserProfilePartialView', $user->getLogin() . ' - Profile');
            $view->addStyle('blog/post.css');
            $view->renderPartial($data);
        }
    }
}
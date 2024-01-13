<?php
namespace App\Controller;

use App\Entity\EmailActivationCode;
use App\Lib\Controller\BaseController;
use App\Lib\View\View;

class AccountController extends BaseController
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
            error_log('test');
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
        $view = new View('Account/ActivationPartialView', 'Account - Activation info');
        $view->addStyle('error.css');
        $view->renderPartial(['message' => $message]);
    }
}